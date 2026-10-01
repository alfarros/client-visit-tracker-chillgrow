@extends('layouts.app')

@section('title', $formTitle)
@section('eyebrow', 'BUKU INDUK PASIEN')

@section('content')
    <div class="page-heading compact-heading"><div><a class="back-link" href="{{ route('pasien.index') }}">← Kembali ke data pasien</a><h1>{{ $formTitle }}</h1><p class="muted">Isi informasi dasar pasien.</p></div></div>
    <section class="panel form-panel"><form method="POST" action="{{ $formAction }}" class="form-stack">
        @csrf
        @if ($formMethod !== 'POST') @method($formMethod) @endif
        <div class="form-intro"><span class="required-note">* Wajib diisi</span></div>
        <div class="field"><label for="nama_lengkap">Nama Lengkap <span class="required-mark">*</span></label><input id="nama_lengkap" name="nama_lengkap" type="text" maxlength="150" value="{{ old('nama_lengkap', $pasien->nama_lengkap) }}" required autofocus>@error('nama_lengkap')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div class="field"><label for="no_rekam_medis">No. Rekam Medis <span class="required-mark">*</span></label><input id="no_rekam_medis" name="no_rekam_medis" type="text" maxlength="30" value="{{ old('no_rekam_medis', $pasien->no_rekam_medis) }}" placeholder="Contoh: 001234AB" autocomplete="off" required><span class="field-hint">Huruf dan angka, maksimal 30 karakter. Angka nol di depan tetap tersimpan.</span>@error('no_rekam_medis')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div class="field-grid"><div class="field"><label for="tanggal_lahir">Tanggal Lahir <span class="required-mark">*</span></label><input id="tanggal_lahir" name="tanggal_lahir" type="date" max="{{ now()->toDateString() }}" value="{{ old('tanggal_lahir', $pasien->tanggal_lahir?->format('Y-m-d')) }}" required>@error('tanggal_lahir')<p class="field-error">{{ $message }}</p>@enderror</div><div class="field"><label for="diagnosa_awal">Diagnosa Awal</label><input id="diagnosa_awal" name="diagnosa_awal" type="text" maxlength="255" value="{{ old('diagnosa_awal', $pasien->diagnosa_awal) }}" placeholder="Opsional">@error('diagnosa_awal')<p class="field-error">{{ $message }}</p>@enderror</div></div>
        <div class="form-actions"><a class="button button-secondary" href="{{ route('pasien.index') }}">Batal</a><button class="button button-primary" type="submit">Simpan</button></div>
    </form></section>
@endsection
