<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiBulanan;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EvaluasiBulananController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $evaluasi = EvaluasiBulanan::create($this->validatedData($request));

        return $this->redirectToProgress($evaluasi, 'Evaluasi bulanan berhasil disimpan.');
    }

    public function update(Request $request, EvaluasiBulanan $evaluasiBulanan): RedirectResponse
    {
        $evaluasiBulanan->update($this->validatedData($request, $evaluasiBulanan));

        return $this->redirectToProgress($evaluasiBulanan, 'Evaluasi bulanan berhasil diperbarui.');
    }

    public function destroy(EvaluasiBulanan $evaluasiBulanan): RedirectResponse
    {
        $pasienId = $evaluasiBulanan->pasien_id;
        $evaluasiBulanan->delete();

        return redirect()
            ->to(route('pasien.show', $pasienId).'#progres-bulanan')
            ->with('success', 'Evaluasi bulanan berhasil dihapus.');
    }

    private function validatedData(Request $request, ?EvaluasiBulanan $evaluasiBulanan = null): array
    {
        $data = $request->validate([
            'pasien_id' => ['required', 'integer', 'exists:pasiens,id'],
            'periode_tanggal' => ['required', 'date'],
            'refleks_primitif' => ['nullable', 'string', 'max:65535'],
            'sensori' => ['nullable', 'string', 'max:65535'],
            'motorik_kasar' => ['nullable', 'string', 'max:65535'],
            'motorik_halus' => ['nullable', 'string', 'max:65535'],
            'kognitif_perseptual' => ['nullable', 'string', 'max:65535'],
            'kemandirian' => ['nullable', 'string', 'max:65535'],
        ], [
            'pasien_id.required' => 'Pasien wajib dipilih.',
            'pasien_id.exists' => 'Pasien yang dipilih tidak ditemukan.',
            'periode_tanggal.required' => 'Periode evaluasi wajib diisi.',
            'periode_tanggal.date' => 'Masukkan periode evaluasi yang valid.',
        ]);

        $periode = Carbon::parse($data['periode_tanggal']);
        $data['periode_tanggal'] = $periode->copy()->startOfMonth()->toDateString();

        if ($evaluasiBulanan) {
            $data['pasien_id'] = $evaluasiBulanan->pasien_id;
        }

        $hasExistingPeriod = EvaluasiBulanan::query()
            ->where('pasien_id', $data['pasien_id'])
            ->whereBetween('periode_tanggal', [$periode->copy()->startOfMonth(), $periode->copy()->endOfMonth()])
            ->when($evaluasiBulanan, fn ($query) => $query->whereKeyNot($evaluasiBulanan->id))
            ->exists();

        if ($hasExistingPeriod) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'periode_tanggal' => 'Evaluasi untuk bulan ini sudah ada. Silakan edit evaluasi yang sudah tersimpan.',
            ]);
        }

        return $data;
    }

    private function redirectToProgress(EvaluasiBulanan $evaluasiBulanan, string $message): RedirectResponse
    {
        return redirect()
            ->to(route('pasien.show', $evaluasiBulanan->pasien_id).'#progres-bulanan')
            ->with('success', $message);
    }
}
