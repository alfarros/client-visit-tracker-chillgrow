<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk · Client Visit Tracker</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="login-page">
    <main class="login-layout">
        <section class="login-brand-panel">
            <div class="brand brand-light"><span class="brand-mark">CV</span><span><strong>Client Visit</strong><small>Tracker</small></span></div>
            <div class="login-welcome"><span class="eyebrow light-eyebrow">OPERASIONAL TERAPI OKUPASI</span><h1>Jadwal kunjungan, lebih tertata.</h1><p>Catat dan temukan jadwal kunjungan klien dengan mudah.</p></div>
            <div class="brand-panel-footer">Ruang kerja internal klinik</div>
        </section>
        <section class="login-form-panel">
            <div class="login-form-wrap">
                <div class="mobile-login-brand brand"><span class="brand-mark">CV</span><span><strong>Client Visit</strong><small>Tracker</small></span></div>
                <span class="eyebrow">SELAMAT DATANG</span>
                <h2>Masuk ke akun Anda</h2>
                <p class="muted">Gunakan username dan password yang diberikan admin.</p>
                <form method="POST" action="{{ route('login.store') }}" class="form-stack login-form">
                    @csrf
                    <div class="field">
                        <label for="username">Username</label>
                        <input id="username" name="username" type="text" value="{{ old('username') }}" autocomplete="username" required autofocus>
                        @error('username')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="field">
                        <label for="password">Password</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required>
                        @error('password')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <label class="check-row"><input type="checkbox" name="remember" value="1" @checked(old('remember'))><span>Ingat Saya</span></label>
                    <button class="button button-primary button-wide" type="submit">Masuk <span aria-hidden="true">→</span></button>
                </form>
                <p class="login-help">Butuh akses? Hubungi admin klinik.</p>
            </div>
        </section>
    </main>
</body>
</html>
