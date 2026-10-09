<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ExportsMedicalRecordWord;
use App\Models\KunjunganKlien;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProgramTerapisController extends Controller
{
    use ExportsMedicalRecordWord;

    public function update(Request $request, KunjunganKlien $kunjunganKlien): RedirectResponse
    {
        abort_if($kunjunganKlien->status === 'Selesai', 403, 'Kunjungan yang selesai bersifat hanya-baca.');

        $data = $request->validate([
            'long_term_goals' => ['required', 'string', 'max:10000'],
            'short_term_goals' => ['required', 'string', 'max:10000'],
            'aktivitas_hari_ini' => ['required', 'string', 'max:10000'],
            'respon_anak' => ['required', 'string', 'max:10000'],
        ], ['*.required' => ':attribute wajib diisi.']);

        $program = $kunjunganKlien->programTerapis()->orderByDesc('created_at')->first();
        $program ? $program->update($data) : $kunjunganKlien->programTerapis()->create($data);

        return redirect()->route('pasien.show', ['pasien' => $kunjunganKlien->pasien_id, 'kunjungan' => $kunjunganKlien->id, 'tab' => 'program-terapi'])
            ->with('success', 'Lembar program terapi berhasil disimpan.');
    }

    public function exportWord(KunjunganKlien $kunjunganKlien): BinaryFileResponse
    {
        $kunjunganKlien->loadMissing('pasien');

        $program = $kunjunganKlien->programTerapis()->orderByDesc('created_at')->first();

        if ($program === null) {
            abort(404, 'Lembar Program Terapi belum tersedia untuk kunjungan ini.');
        }

        $values = array_merge($this->patientWordValues($kunjunganKlien), [
            'long_term_goals' => $program->long_term_goals,
            'short_term_goals' => $program->short_term_goals,
            'aktivitas_hari_ini' => $program->aktivitas_hari_ini,
            'respon_anak' => $program->respon_anak,
        ]);

        $filename = sprintf(
            'ProgramTerapi_%s_%s.docx',
            $this->sanitizeFilenamePart($kunjunganKlien->pasien?->nama_lengkap ?? ''),
            $kunjunganKlien->tanggal_kunjungan->format('Y-m-d'),
        );

        return $this->downloadWordFromTemplate('program-terapi.docx', $values, $filename);
    }
}
