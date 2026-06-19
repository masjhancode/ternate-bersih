<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk — Ternate Bersih</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            padding: 1rem;
            -webkit-font-smoothing: antialiased;
        }
        .login-card {
            width: 100%;
            max-width: 380px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 8px 24px rgba(0,0,0,0.06);
            padding: 2.25rem 2rem 2rem;
        }
        .logo-area {
            text-align: center;
            margin-bottom: 1.75rem;
        }
        .logo-icon {
            width: 48px; height: 48px;
            background: #059669;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.75rem;
        }
        .logo-icon svg { width: 26px; height: 26px; color: #fff; }
        .logo-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #111827;
            letter-spacing: -0.01em;
        }
        .logo-sub {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 0.2rem;
        }
        .form-group { margin-bottom: 0.875rem; }
        .form-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.35rem;
        }
        .form-input {
            width: 100%;
            padding: 0.625rem 0.75rem;
            font-size: 0.8125rem;
            font-family: inherit;
            color: #1f2937;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-input:focus {
            border-color: #059669;
            box-shadow: 0 0 0 3px rgba(5,150,105,0.1);
            background: #fff;
        }
        .form-input::placeholder { color: #c7cdd4; }
        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            margin-top: 0.25rem;
        }
        .remember {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8125rem;
            color: #6b7280;
            cursor: pointer;
        }
        .remember input { accent-color: #059669; width: 14px; height: 14px; }
        .forgot {
            font-size: 0.8125rem;
            color: #059669;
            text-decoration: none;
            font-weight: 500;
        }
        .forgot:hover { color: #047857; }
        .btn {
            width: 100%;
            padding: 0.65rem;
            background: #059669;
            color: #fff;
            font-size: 0.8125rem;
            font-weight: 600;
            font-family: inherit;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s;
        }
        .btn:hover {
            background: #047857;
            box-shadow: 0 4px 12px rgba(5,150,105,0.25);
        }
        .alt {
            text-align: center;
            margin-top: 1.25rem;
            font-size: 0.8125rem;
            color: #9ca3af;
        }
        .alt a { color: #059669; text-decoration: none; font-weight: 600; }
        .alt a:hover { color: #047857; }
        .err { list-style: none; margin-top: 0.3rem; }
        .err li { font-size: 0.75rem; color: #dc2626; }
        .status { font-size: 0.8125rem; color: #059669; margin-bottom: 0.75rem; text-align: center; }
        .footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.6875rem;
            color: #c7cdd4;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo-area">
            <div class="logo-icon">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12.75 3.03v.568c0 .334.148.65.405.864a4.5 4.5 0 010 6.136.723.723 0 00-.405.864v.568M12.75 3.03A9 9 0 003 12a9 9 0 009.75 8.97M12.75 3.03A9 9 0 0121 12a9.004 9.004 0 01-8.25 8.97m0-17.94A9 9 0 003 12a9 9 0 009.75 8.97" /></svg>
            </div>
            <div class="logo-title">Ternate Bersih</div>
            <div class="logo-sub">Sistem Pelaporan Sampah</div>
        </div>

        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input class="form-input" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com">
                @if ($errors->has('email'))
                    <ul class="err">@foreach($errors->get('email') as $e)<li>{{ $e }}</li>@endforeach</ul>
                @endif
            </div>
            <div class="form-group">
                <label class="form-label" for="password">Kata Sandi</label>
                <input class="form-input" id="password" type="password" name="password" required placeholder="••••••••">
                @if ($errors->has('password'))
                    <ul class="err">@foreach($errors->get('password') as $e)<li>{{ $e }}</li>@endforeach</ul>
                @endif
            </div>
            <div class="form-row">
                <label class="remember"><input type="checkbox" name="remember"> Ingat saya</label>
                @if (Route::has('password.request'))
                    <a class="forgot" href="{{ route('password.request') }}">Lupa sandi?</a>
                @endif
            </div>
            <button type="submit" class="btn">Masuk</button>
        </form>

        <!-- Tombol Daftar Dinonaktifkan Sementara (Sistem Tertutup) -->
        <div class="footer">&copy; {{ date('Y') }} Dinas Lingkungan Hidup Kota Ternate</div>
    </div>
</body>
</html>
