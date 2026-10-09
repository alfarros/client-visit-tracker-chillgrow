<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ExportsMedicalRecordWord;
use App\Models\KunjunganKlien;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CpptController extends Controller
{
    use ExportsMedicalRecordWord;

    public function show(KunjunganKlien $kunjunganKlien): RedirectResponse
    {
        return redirect()->route('pasien.show', ['pasien' => $kunjunganKlien->pasien_id, 'kunjungan' => $kunjunganKlien->id, 'tab' => 'cppt']);
    }

    public function update(Request $request, KunjunganKlien $kunjunganKlien): RedirectResponse
    {
        abort_if($kunjunganKlien->status === 'Selesai', 403, 'CPPT kunjungan yang selesai bersifat hanya-baca.');

        $data = $request->validate([
            'tanggal' => ['required', 'date_format:Y-m-d'],
            'penanggung_jawab' => ['required', 'string', 'max:150'],
            'subjective' => ['required', 'string', 'max:10000'],
            'objective' => ['required', 'string', 'max:10000'],
            'assessment' => ['required', 'string', 'max:10000'],
            'planning' => ['required', 'string', 'max:10000'],
        ], ['*.required' => ':attribute wajib diisi.']);

        $cppt = $kunjunganKlien->cppts()->orderByDesc('tanggal')->first();
        $cppt ? $cppt->update($data) : $kunjunganKlien->cppts()->create($data);

        return redirect()->route('pasien.show', ['pasien' => $kunjunganKlien->pasien_id, 'kunjungan' => $kunjunganKlien->id, 'tab' => 'cppt'])
            ->with('success', 'CPPT berhasil disimpan.');
    }

    public function exportWord(KunjunganKlien $kunjunganKlien): BinaryFileResponse
    {
        $kunjunganKlien->loadMissing('pasien');

        $cppt = $kunjunganKlien->cppts()->orderByDesc('tanggal')->first();

        if ($cppt === null) {
            abort(404, 'CPPT belum tersedia untuk kunjungan ini.');
        }

        $values = array_merge($this->patientWordValues($kunjunganKlien), [
            'tanggal_catatan' => $cppt->tanggal?->locale('id')->translatedFormat('d F Y') ?: '—',
            'penanggung_jawab' => $cppt->penanggung_jawab,
            'subjective' => $cppt->subjective,
            'objective' => $cppt->objective,
            'assessment' => $cppt->assessment,
            'planning' => $cppt->planning,
        ]);

        $filename = sprintf(
            'CPPT_%s_%s.docx',
            $this->sanitizeFilenamePart($kunjunganKlien->pasien?->nama_lengkap ?? ''),
            $kunjunganKlien->tanggal_kunjungan->format('Y-m-d'),
        );

        return $this->downloadWordFromTemplate('cppt.docx', $values, $filename);
    }
}
