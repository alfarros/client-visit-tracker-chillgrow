<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Client Visit Tracker') · Client Visit Tracker</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar" id="sidebar">
        <a class="brand" href="{{ route('dashboard') }}">
            <span class="brand-mark" aria-hidden="true">CV</span>
            <span><strong>Client Visit</strong><small>Tracker</small></span>
        </a>
        <div class="nav-label">MENU UTAMA</div>
        <nav class="nav-list" aria-label="Navigasi utama">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <span class="nav-icon" aria-hidden="true">⌂</span> Dashboard
            </a>
            <a class="nav-link {{ request()->routeIs('pasien.*') ? 'active' : '' }}" href="{{ route('pasien.index') }}">
                <span class="nav-icon" aria-hidden="true">♙</span> Pasien
            </a>
            <a class="nav-link {{ request()->routeIs('kunjungan.*') ? 'active' : '' }}" href="{{ route('kunjungan.index') }}">
                <span class="nav-icon" aria-hidden="true">▤</span> Kunjungan
            </a>
        </nav>
        <div class="sidebar-bottom">
            <div class="profile-chip"><span class="avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span><span><strong>{{ auth()->user()->name }}</strong><small>{{ ucfirst(auth()->user()->role) }}</small></span></div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="nav-link logout-link" type="submit"><span class="nav-icon" aria-hidden="true">↪</span> Keluar</button>
            </form>
        </div>
    </aside>

    <main class="main-area">
        <header class="topbar">
            <button class="menu-toggle" type="button" aria-label="Buka navigasi" aria-expanded="false" aria-controls="sidebar">☰</button>
            <span class="topbar-title">@yield('eyebrow', 'Operasional Klinik')</span>
            <div class="topbar-user"><span class="status-dot"></span>{{ auth()->user()->name }}</div>
        </header>
        <div class="page-content">
            @yield('content')
        </div>
    </main>
</div>

<div class="toast" role="status" aria-live="polite" data-toast hidden>
    <span class="toast-icon" aria-hidden="true" data-toast-icon>✓</span><span data-toast-message></span>
</div>

<div class="modal-backdrop" data-delete-modal hidden>
    <section class="confirm-modal" role="dialog" aria-modal="true" aria-labelledby="delete-title" aria-describedby="delete-description">
        <div class="modal-icon" aria-hidden="true">!</div>
        <h2 id="delete-title">Hapus data ini?</h2>
        <p id="delete-description">Data untuk <strong data-delete-name></strong> akan dihapus.</p>
        <div class="modal-actions">
            <button class="button button-secondary" type="button" data-delete-cancel>Batal</button>
            <form method="POST" data-delete-form>
                @csrf
                @method('DELETE')
                <button class="button button-danger" type="submit">Hapus</button>
            </form>
        </div>
    </section>
</div>

<script src="{{ asset('js/app.js') }}" defer></script>
@if (session('success'))
    <script>document.addEventListener('DOMContentLoaded', () => window.showTrackerToast(@js(session('success'))));</script>
@endif
@if (session('error'))
    <script>document.addEventListener('DOMContentLoaded', () => window.showTrackerToast(@js(session('error')), 'error'));</script>
@endif
</body>
</html>
