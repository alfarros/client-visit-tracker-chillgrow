<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PasienController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100']]);
        $pasiens = Pasien::query()
            ->when($filters['q'] ?? null, function ($query, string $term): void {
                $query->where('nama_lengkap', 'like', '%'.$term.'%')
                    ->orWhere('no_rekam_medis', 'like', '%'.$term.'%');
            })
            ->orderBy('nama_lengkap')
            ->paginate(15)
            ->withQueryString();

        return view('pasiens.index', compact('pasiens', 'filters'));
    }

    public function create(): View
    {
        return view('pasiens.form', [
            'pasien' => new Pasien(),
            'formTitle' => 'Tambah Pasien',
            'formAction' => route('pasien.store'),
            'formMethod' => 'POST',
            'cancelUrl' => route('pasien.index'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $pasien = Pasien::create($this->validatedData($request));

        return redirect()->route('pasien.show', $pasien)->with('success', 'Data pasien berhasil disimpan.');
    }

    public function show(Request $request, int $id): View
    {
        $pasien = Pasien::with([
            'kunjungan' => fn ($query) => $query->orderByDesc('tanggal_kunjungan'),
            'evaluasiBulanan' => fn ($query) => $query->orderByDesc('periode_tanggal'),
        ])->findOrFail($id);

        $kunjunganDiminta = $request->filled('kunjungan')
            ? $pasien->kunjungan->firstWhere('id', (int) $request->query('kunjungan'))
            : null;
        $melihatRiwayat = $kunjunganDiminta !== null;
        $kunjunganAktif = $kunjunganDiminta
            ?? $pasien->kunjungan->firstWhere('status', 'Berlangsung')
            ?? $pasien->kunjungan
                ->filter(fn ($kunjungan) => $kunjungan->status === 'Antre'
                    && $kunjungan->tanggal_kunjungan->greaterThanOrEqualTo(today()))
                ->sortBy(fn ($kunjungan) => $kunjungan->tanggal_kunjungan->format('Y-m-d').' '.($kunjungan->jam_kunjungan ?? '23:59:59'))
                ->first();
        $cppt = null;
        $programTerapis = null;

        if ($kunjunganAktif) {
            $kunjunganAktif->load(['cppts' => fn ($query) => $query->orderByDesc('tanggal'), 'programTerapis']);
            $cppt = $kunjunganAktif->cppts->first();
            $programTerapis = $kunjunganAktif->programTerapis->sortByDesc('created_at')->first();
        }

        return view('pasiens.show', compact('pasien', 'kunjunganAktif', 'cppt', 'programTerapis', 'melihatRiwayat'));
    }

    public function edit(Pasien $pasien): View
    {
        return view('pasiens.form', [
            'pasien' => $pasien,
            'formTitle' => 'Edit Data Pasien',
            'formAction' => route('pasien.update', $pasien),
            'formMethod' => 'PUT',
            'cancelUrl' => route('pasien.show', $pasien),
        ]);
    }

    public function update(Request $request, Pasien $pasien): RedirectResponse
    {
        $pasien->update($this->validatedData($request, $pasien));

        return redirect()->route('pasien.show', $pasien)->with('success', 'Data pasien berhasil diperbarui.');
    }

    public function destroy(Pasien $pasien): RedirectResponse
    {
        if ($pasien->kunjungan()->exists()) {
            return redirect()->route('pasien.index')->with('error', 'Pasien yang memiliki riwayat kunjungan tidak dapat dihapus.');
        }

        $pasien->delete();

        return redirect()->route('pasien.index')->with('success', 'Data pasien berhasil dihapus.');
    }

    private function validatedData(Request $request, ?Pasien $pasien = null): array
    {
        return $request->validate([
            'no_rekam_medis' => ['required', 'string', 'alpha_num', 'max:30', 'unique:pasiens,no_rekam_medis'.($pasien ? ','.$pasien->id : '')],
            'nama_lengkap' => ['required', 'string', 'max:150'],
            'tanggal_lahir' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'diagnosa_awal' => ['nullable', 'string', 'max:255'],
        ], [
            'no_rekam_medis.required' => 'No. RM wajib diisi.',
            'no_rekam_medis.alpha_num' => 'No. RM hanya boleh berisi huruf dan angka.',
            'no_rekam_medis.unique' => 'No. RM sudah digunakan pasien lain.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.date_format' => 'Masukkan tanggal lahir yang valid.',
            'tanggal_lahir.before_or_equal' => 'Tanggal lahir tidak boleh melebihi hari ini.',
        ]);
    }
}
