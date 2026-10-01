<?php

namespace App\Http\Controllers;

use App\Models\KunjunganKlien;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CpptController extends Controller
{
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
}
