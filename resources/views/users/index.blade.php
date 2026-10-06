@extends('layouts.app')

@section('title', 'Manajemen Admin')
@section('eyebrow', 'PENGATURAN AKUN')

@section('content')
    <div class="page-heading">
        <div>
            <span class="eyebrow">AKSES PENGGUNA</span>
            <h1>Manajemen Admin</h1>
            <p class="muted">Kelola akun dan peran pengguna aplikasi.</p>
        </div>
        <button class="button button-primary" type="button" data-user-modal-open>＋ Tambah Pengguna</button>
    </div>

    <section class="panel">
        <div class="table-meta"><strong>{{ $penggunas->total() }} pengguna</strong><span>Daftar akun aplikasi</span></div>
        @if ($penggunas->isEmpty())
            <div class="empty-state"><strong>Belum ada pengguna</strong>
                <p>Tambahkan akun admin untuk mulai mengelola akses.</p>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Nomor</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Peran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($penggunas as $pengguna)
                            <tr>
                                <td class="cell-id">{{ $penggunas->firstItem() + $loop->index }}</td>
                                <td class="cell-name">{{ $pengguna->name }}</td>
                                <td class="cell-mono">{{ $pengguna->username }}</td>
                                <td>{{ $pengguna->isSuperAdmin() ? 'Super Admin' : 'Admin' }}</td>
                                <td class="row-actions">
                                    <a class="button button-secondary button-small"
                                        href="{{ route('users.edit', $pengguna) }}">Edit</a>
                                    <button class="button button-secondary button-small danger-text" type="button"
                                        data-delete-trigger data-action="{{ route('users.destroy', $pengguna) }}"
                                        data-name="akun {{ $pengguna->username }}" title="Hapus pengguna"
                                        aria-label="Hapus pengguna {{ $pengguna->username }}">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($penggunas->hasPages())
                <div class="pagination-wrap custom-pagination">
                    <span>Halaman {{ $penggunas->currentPage() }} dari {{ $penggunas->lastPage() }}</span>
                    <div>
                        @if ($penggunas->onFirstPage())
                        <span class="page-disabled">← Sebelumnya</span>@else<a
                                href="{{ $penggunas->previousPageUrl() }}">← Sebelumnya</a>
                        @endif
                        @if ($penggunas->hasMorePages())
                        <a href="{{ $penggunas->nextPageUrl() }}">Berikutnya →</a>@else<span
                                class="page-disabled">Berikutnya →</span>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </section>

    <div class="modal-backdrop evaluation-modal-backdrop" data-user-modal
        @if ($errors->hasAny(['name', 'username', 'role', 'password'])) data-reopen="true" @endif hidden>
        <section class="evaluation-modal" role="dialog" aria-modal="true" aria-labelledby="user-modal-title">
            <div class="evaluation-modal-heading">
                <div><span class="eyebrow">AKSES PENGGUNA</span>
                    <h2 id="user-modal-title">Tambah Pengguna</h2>
                </div>
                <button class="modal-close" type="button" aria-label="Tutup" data-user-modal-close>×</button>
            </div>
            <form method="POST" action="{{ route('users.store') }}" class="form-stack">
                @csrf
                <div class="field-grid">
                    <div class="field">
                        <label for="user-name">Nama</label>
                        <input id="user-name" name="name" value="{{ old('name') }}" maxlength="150" required>
                        @error('name')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="field">
                        <label for="user-username">Username</label>
                        <input id="user-username" name="username" value="{{ old('username') }}" minlength="3"
                            maxlength="80" pattern="[a-z0-9._-]+" required>
                        @error('username')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="field">
                        <label for="user-role">Peran</label>
                        <select id="user-role" name="role" required>
                            <option value="admin" @selected(old('role', 'admin') === 'admin')>Admin</option>
                            <option value="super_admin" @selected(old('role') === 'super_admin')>Super Admin</option>
                        </select>
                        @error('role')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="field">
                        <label for="user-password">Kata sandi</label>
                        <input id="user-password" name="password" type="password" minlength="12" autocomplete="new-password"
                            required>
                        @error('password')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="field">
                        <label for="user-password-confirmation">Konfirmasi kata sandi</label>
                        <input id="user-password-confirmation" name="password_confirmation" type="password" minlength="12"
                            autocomplete="new-password" required>
                    </div>
                </div>
                <div class="form-actions">
                    <button class="button button-secondary" type="button" data-user-modal-close>Batal</button>
                    <button class="button button-primary" type="submit">Simpan</button>
                </div>
            </form>
        </section>
    </div>
@endsection
