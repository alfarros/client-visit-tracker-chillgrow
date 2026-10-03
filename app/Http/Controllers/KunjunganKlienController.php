<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKunjunganKlienRequest;
use App\Http\Requests\UpdateKunjunganKlienRequest;
use App\Models\KunjunganKlien;
use App\Models\Pasien;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KunjunganKlienController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'tanggal_mulai' => ['nullable', 'date_format:Y-m-d'],
            'tanggal_selesai' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:tanggal_mulai'],
        ], [
            'tanggal_mulai.date_format' => 'Tanggal mulai tidak valid.',
            'tanggal_selesai.date_format' => 'Tanggal selesai tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ]);

        $kunjungan = KunjunganKlien::query()
            ->when($filters['q'] ?? null, function ($query, string $term): void {
                $query->whereHas('pasien', function ($patientQuery) use ($term): void {
                    $patientQuery->where('nama_lengkap', 'like', '%'.$term.'%')
                        ->orWhere('no_rekam_medis', 'like', '%'.$term.'%');
                });
            })
            ->when($filters['tanggal_mulai'] ?? null, fn ($query, string $from) =>
                $query->whereDate('tanggal_kunjungan', '>=', $from))
            ->when($filters['tanggal_selesai'] ?? null, fn ($query, string $to) =>
                $query->whereDate('tanggal_kunjungan', '<=', $to))
            ->with('pasien')
            ->orderByDesc('tanggal_kunjungan')
            ->orderBy('jam_kunjungan')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('kunjungan-klien.index', compact('kunjungan', 'filters'));
    }

    public function create(Request $request): View
    {
        $pasienId = $request->query('pasien_id');

        return view('kunjungan-klien.form', [
            'kunjunganKlien' => new KunjunganKlien(['pasien_id' => $pasienId]),
            'formTitle' => 'Tambah Kunjungan',
            'formAction' => route('kunjungan.store'),
            'formMethod' => 'POST',
            'cancelUrl' => $pasienId && Pasien::whereKey($pasienId)->exists()
                ? route('pasien.show', $pasienId)
                : route('kunjungan.index'),
            'pasiens' => Pasien::select('id', 'nama_lengkap', 'no_rekam_medis')->orderBy('nama_lengkap')->get(),
        ]);
    }

    public function store(StoreKunjunganKlienRequest $request): RedirectResponse
    {
        KunjunganKlien::create($request->validated());

        return redirect()->route('kunjungan.index')->with('success', 'Data berhasil disimpan.');
    }

    public function edit(KunjunganKlien $kunjunganKlien): View
    {
        return view('kunjungan-klien.form', [
            'kunjunganKlien' => $kunjunganKlien,
            'formTitle' => 'Edit Kunjungan',
            'formAction' => route('kunjungan.update', $kunjunganKlien),
            'formMethod' => 'PUT',
            'cancelUrl' => route('kunjungan.index'),
            'pasiens' => Pasien::select('id', 'nama_lengkap', 'no_rekam_medis')->orderBy('nama_lengkap')->get(),
        ]);
    }

    public function update(UpdateKunjunganKlienRequest $request, KunjunganKlien $kunjunganKlien): RedirectResponse
    {
        $kunjunganKlien->update($request->validated());

        return redirect()->route('kunjungan.index')->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy(KunjunganKlien $kunjunganKlien): RedirectResponse
    {
        if ($kunjunganKlien->cppts()->exists() || $kunjunganKlien->programTerapis()->exists()) {
            return redirect()->route('kunjungan.index')->with('error', 'Kunjungan dengan catatan terapi tidak dapat dihapus.');
        }

        $kunjunganKlien->delete();

        return redirect()->route('kunjungan.index')->with('success', 'Data berhasil dihapus.');
    }

    public function complete(KunjunganKlien $kunjunganKlien): RedirectResponse
    {
        if ($kunjunganKlien->status === 'Batal') {
            return redirect()->route('kunjungan.index')->with('error', 'Kunjungan yang dibatalkan tidak dapat diselesaikan.');
        }

        if ($kunjunganKlien->status !== 'Berlangsung') {
            return redirect()->route('pasien.show', ['pasien' => $kunjunganKlien->pasien_id, 'kunjungan' => $kunjunganKlien->id])
                ->with('error', 'Terapi belum mencapai jam jadwalnya. Status akan berubah otomatis saat jadwal tiba.');
        }

        if (! $kunjunganKlien->cppts()->exists() || ! $kunjunganKlien->programTerapis()->exists()) {
            return redirect()->route('pasien.show', ['pasien' => $kunjunganKlien->pasien_id, 'kunjungan' => $kunjunganKlien->id])
                ->with('error', 'Lengkapi dan simpan Lembar Program Terapi serta CPPT sebelum menyelesaikan sesi terapi.');
        }

        $kunjunganKlien->update(['status' => 'Selesai']);

        return redirect()->route('pasien.show', [$kunjunganKlien->pasien_id, 'kunjungan' => $kunjunganKlien->id])
            ->with('success', 'Sesi terapi ditandai selesai. Catatan kini hanya dapat dilihat.');
    }
}
