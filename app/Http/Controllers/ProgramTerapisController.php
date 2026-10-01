<?php

namespace App\Http\Controllers;

use App\Models\KunjunganKlien;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProgramTerapisController extends Controller
{
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
}
