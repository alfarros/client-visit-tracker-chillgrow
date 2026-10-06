<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1f70b7">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="EMR Klinik">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/apple-touch-icon.png') }}">
    <title>@yield('title', 'Electronic Medical Record ChilGrow    ') · EMR ChilGrow</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
    <div class="app-shell">
        <aside class="sidebar" id="sidebar">
            <a class="brand" href="{{ route('dashboard') }}">
                <img class="brand-logo" src="{{ asset('icons/chilgrow.jpg') }}" alt="ChilGrow">
                <span><strong>Electronic Medical Record</strong><small>ChilGrow</small></span>
            </a>
            <div class="nav-label">MENU UTAMA</div>
            <nav class="nav-list" aria-label="Navigasi utama">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                    href="{{ route('dashboard') }}">
                    <span class="nav-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="M13 8V4q0-.425.288-.712T14 3h6q.425 0 .713.288T21 4v4q0 .425-.288.713T20 9h-6q-.425 0-.712-.288T13 8M3 12V4q0-.425.288-.712T4 3h6q.425 0 .713.288T11 4v8q0 .425-.288.713T10 13H4q-.425 0-.712-.288T3 12m10 8v-8q0-.425.288-.712T14 11h6q.425 0 .713.288T21 12v8q0 .425-.288.713T20 21h-6q-.425 0-.712-.288T13 20M3 20v-4q0-.425.288-.712T4 15h6q.425 0 .713.288T11 16v4q0 .425-.288.713T10 21H4q-.425 0-.712-.288T3 20m2-9h4V5H5zm10 8h4v-6h-4zm0-12h4V5h-4zM5 19h4v-2H5zm4-2" />
                        </svg></span> Dashboard
                </a>
                <a class="nav-link {{ request()->routeIs('pasien.*') ? 'active' : '' }}"
                    href="{{ route('pasien.index') }}">
                    <span class="nav-icon" aria-hidden="true"><svg class="nav-icon" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" viewBox="0 0 24 24">
                            <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="1.50">
                                <path
                                    d="M20 22v-3c0-2.828 0-4.243-.879-5.121c-.878-.88-2.293-.88-5.121-.88h-4c-2.828 0-4.243 0-5.121.88C4 14.757 4 16.172 4 18.999c0 .933 0 1.399.152 1.766a2 2 0 0 0 1.083 1.083C5.602 22 6.068 22 7 22m2.5-9l3 9M7 13.5V22" />
                                <path
                                    d="M12 19h2.5a1.5 1.5 0 0 1 0 3h-2m3-15.5v-1a3.5 3.5 0 1 0-7 0v1a3.5 3.5 0 1 0 7 0" />
                            </g>
                        </svg></span> Pasien
                </a>
                <a class="nav-link {{ request()->routeIs('kunjungan.*') ? 'active' : '' }}"
                    href="{{ route('kunjungan.index') }}">
                    <span class="nav-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24">
                            <path fill="currentColor" fill-rule="evenodd"
                                d="M17.652 7.264a3.484 3.484 0 1 0 0-6.968a3.484 3.484 0 0 0 0 6.968m4.115 3.182a5.82 5.82 0 0 1 1.704 4.114v1.494a1 1 0 0 1-1 1h-1.494l-.721 5.774a1 1 0 0 1-.993.876h-3.222a1 1 0 0 1-.992-.876l-.032-.255v-5.399a2.5 2.5 0 0 0-.8-1.843l-2.238-2.064a5.819 5.819 0 0 1 9.788-2.821m-8.25 6.724a1 1 0 0 0-.32-.739l-5.496-5.07a1 1 0 0 0-1.356 0L.85 16.431a1 1 0 0 0-.32.74v5.554a1 1 0 0 0 .999 1h10.99a1 1 0 0 0 1-1V17.17ZM7.84 15.041H6.205a.5.5 0 0 0-.5.5v1.738H3.968a.5.5 0 0 0-.5.5v1.635a.5.5 0 0 0 .5.5h1.737v1.738a.5.5 0 0 0 .5.5H7.84a.5.5 0 0 0 .5-.5v-1.738h1.739a.5.5 0 0 0 .5-.5V17.78a.5.5 0 0 0-.5-.5H8.34v-1.738a.5.5 0 0 0-.5-.5Z"
                                clip-rule="evenodd" />
                        </svg></span> Kunjungan
                </a>
                @can('manage-users')
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                        href="{{ route('users.index') }}">
                        <span class="nav-icon" aria-hidden="true">♙</span> Manajemen Admin
                    </a>
                @endcan
                <a class="nav-link {{ request()->routeIs('users.edit', 'users.update') ? 'active' : '' }}"
                    href="{{ route('users.edit', auth()->user()) }}">
                    <span class="nav-icon" aria-hidden="true">◎</span> Profil Saya
                </a>
            </nav>
            <div class="sidebar-bottom">
                <div class="profile-chip"><span
                        class="avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span><span><strong>{{ auth()->user()->name }}</strong><small>{{ ucfirst(auth()->user()->role) }}</small></span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="nav-link logout-link" type="submit"><span class="logout-icon"
                            aria-hidden="true"><svg class="logout-icon" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24">
                                <path fill="none" stroke="currentColor" stroke-linecap="round"
                                    stroke-linejoin="round" stroke-width="1.50"
                                    d="M15 4.001H5v14a2 2 0 0 0 2 2h8m1-5l3-3m0 0l-3-3m3 3H9" />
                            </svg></span> Keluar</button>
                </form>
            </div>
        </aside>

        <main class="main-area">
            <header class="topbar">
                <button class="menu-toggle" type="button" aria-label="Buka navigasi" aria-expanded="false"
                    aria-controls="sidebar">☰</button>
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
        <section class="confirm-modal" role="dialog" aria-modal="true" aria-labelledby="confirm-title"
            aria-describedby="confirm-description">
            <div class="modal-icon" aria-hidden="true" data-confirm-icon>!</div>
            <h2 id="confirm-title" data-confirm-title>Hapus data ini?</h2>
            <p id="confirm-description" data-confirm-description>Data yang dipilih akan dihapus.</p>
            <div class="modal-actions">
                <button class="button button-secondary" type="button" data-delete-cancel>Batal</button>
                <form method="POST" data-delete-form>
                    @csrf
                    @method('DELETE')
                    <button class="button button-danger" type="submit" data-confirm-submit>Hapus</button>
                </form>
            </div>
        </section>
    </div>

    <script src="{{ asset('js/app.js') }}" defer></script>
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => window.showTrackerToast(@js(session('success'))));
        </script>
    @endif
    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', () => window.showTrackerToast(@js(session('error')), 'error'));
        </script>
    @endif
</body>

</html>
