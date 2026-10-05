@extends('layouts.app')

@section('title', $formTitle)
@section('eyebrow', 'DATA KUNJUNGAN')

@section('content')
    <div class="page-heading compact-heading">
        <div><a class="back-link" href="{{ $cancelUrl }}">← Kembali</a>
            <h1>{{ $formTitle }}</h1>
            <p class="muted">Isi informasi jadwal terapi klien.</p>
        </div>
    </div>
    <section class="panel form-panel">
        <form method="POST" action="{{ $formAction }}" class="form-stack">
            @csrf
            @if ($formMethod !== 'POST')
                @method($formMethod)
            @endif
            <div class="form-intro"><span class="required-note">* Wajib diisi</span></div>
            
            <div class="field min-w-0">
                <label for="pasien_id">Pasien <span class="required-mark">*</span></label>
                <select id="pasien_id" name="pasien_id" class="w-full max-w-full appearance-none bg-white" required autofocus>
                    <option value="">Pilih pasien</option>
                    @foreach ($pasiens as $pasien)
                        <option value="{{ $pasien->id }}" @selected((string) old('pasien_id', $kunjunganKlien->pasien_id) === (string) $pasien->id)>{{ $pasien->nama_lengkap }} ·
                            {{ $pasien->no_rekam_medis }}</option>
                    @endforeach
                </select>
                @if ($pasiens->isEmpty())
                    <span class="field-hint">Belum ada data pasien. <a class="text-link"
                            href="{{ route('pasien.create') }}">Tambah pasien</a> terlebih dahulu.</span>
                @endif
                @error('pasien_id')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="field-grid">
                <div class="field min-w-0">
                    <label for="tanggal_kunjungan">Tanggal Kunjungan <span class="required-mark">*</span></label>
                    <input id="tanggal_kunjungan" name="tanggal_kunjungan" type="date"
                        class="w-full max-w-full appearance-none bg-white"
                        value="{{ old('tanggal_kunjungan', $kunjunganKlien->tanggal_kunjungan?->format('Y-m-d')) }}"
                        required>
                    @error('tanggal_kunjungan')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="field min-w-0">
                    <label for="jam_kunjungan">Jam Kunjungan @if ($formMethod === 'POST')<span class="required-mark">*</span>@endif</label>
                    <input id="jam_kunjungan" name="jam_kunjungan" type="time"
                        class="w-full max-w-full appearance-none bg-white"
                        value="{{ old('jam_kunjungan', $kunjunganKlien->jam_kunjungan ? substr((string) $kunjunganKlien->jam_kunjungan, 0, 5) : '') }}"
                        @required($formMethod === 'POST')>
                    @if ($formMethod !== 'POST' && ! $kunjunganKlien->jam_kunjungan)
                        <span class="field-hint">Kunjungan ini belum memiliki jam jadwal. Isi agar status dapat berubah otomatis.</span>
                    @endif
                    @error('jam_kunjungan')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="field min-w-0">
                    <label for="cara_bayar">Cara Bayar <span class="required-mark">*</span></label>
                    <select id="cara_bayar" name="cara_bayar" class="w-full max-w-full appearance-none bg-white" required>
                        <option value="">Pilih cara bayar</option>
                        @foreach (['BPJS', 'UMUM', 'ASURANSI'] as $caraBayar)
                            <option value="{{ $caraBayar }}" @selected(old('cara_bayar', $kunjunganKlien->cara_bayar) === $caraBayar)>{{ $caraBayar }}</option>
                        @endforeach
                    </select>
                    @error('cara_bayar')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="form-actions"><a class="button button-secondary"
                    href="{{ $cancelUrl }}">Batal</a><button class="button button-primary"
                    type="submit">Simpan</button></div>
        </form>
    </section>
@endsection