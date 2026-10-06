@extends('layouts.app')

@section('title', 'Edit Pengguna')
@section('eyebrow', 'PENGATURAN AKUN')

@section('content')
    <div class="page-heading compact-heading">
        <div>
            <a class="back-link" href="{{ auth()->user()->isSuperAdmin() ? route('users.index') : route('dashboard') }}">←
                Kembali</a>
            <h1>{{ auth()->user()->is($pengguna) ? 'Profil Saya' : 'Edit Pengguna' }}</h1>
            <p class="muted">Perbarui data akun {{ $pengguna->username }}.</p>
        </div>
    </div>
    <section class="panel form-panel">
        <form method="POST" action="{{ route('users.update', $pengguna) }}" class="form-stack">
            @csrf
            @method('PUT')
            <div class="field-grid">
                <div class="field"><label for="name">Nama</label><input id="name" name="name"
                        value="{{ old('name', $pengguna->name) }}" maxlength="150" required>
                    @error('name')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="field"><label for="username">Username</label><input id="username" name="username"
                        value="{{ old('username', $pengguna->username) }}" minlength="3" maxlength="80"
                        pattern="[a-z0-9._-]+" required>
                    @error('username')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>
                @can('manage-users')
                    <div class="field"><label for="role">Peran</label><select id="role" name="role" required>
                            <option value="admin" @selected(old('role', $pengguna->role) === 'admin')>Admin</option>
                            <option value="super_admin" @selected(old('role', $pengguna->role) === 'super_admin')>Super Admin</option>
                        </select>
                        @error('role')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                @endcan
                <div class="field"><label for="password">Kata sandi baru <span class="field-hint">Kosongkan jika tidak
                            diubah.</span></label><input id="password" name="password" type="password" minlength="12"
                        autocomplete="new-password">
                    @error('password')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="field"><label for="password_confirmation">Konfirmasi kata sandi baru</label><input
                        id="password_confirmation" name="password_confirmation" type="password" minlength="12"
                        autocomplete="new-password"></div>
            </div>
            <div class="form-actions">
                <a class="button button-secondary"
                    href="{{ auth()->user()->isSuperAdmin() ? route('users.index') : route('dashboard') }}">Batal</a>
                <button class="button button-primary" type="submit">Simpan Perubahan</button>
            </div>
        </form>
    </section>
@endsection
