<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Ternate Bersih') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
            body {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }

            .auth-wrapper {
                display: flex;
                min-height: 100vh;
                width: 100%;
            }

            /* ===== Left Branding Panel ===== */
            .auth-branding {
                display: none;
                width: 50%;
                background: linear-gradient(135deg, #065f46 0%, #047857 25%, #059669 50%, #10b981 75%, #34d399 100%);
                position: relative;
                overflow: hidden;
                align-items: center;
                justify-content: center;
                padding: 3rem;
            }
            @media (min-width: 1024px) {
                .auth-branding { display: flex; }
            }
            .auth-branding::before {
                content: '';
                position: absolute;
                top: -50%; left: -50%;
                width: 200%; height: 200%;
                background: radial-gradient(circle at 30% 70%, rgba(255,255,255,0.06) 0%, transparent 50%),
                            radial-gradient(circle at 70% 30%, rgba(255,255,255,0.04) 0%, transparent 50%);
                animation: floatBg 20s ease-in-out infinite;
            }
            @keyframes floatBg {
                0%, 100% { transform: translate(0, 0) rotate(0deg); }
                25% { transform: translate(2%, -2%) rotate(1deg); }
                50% { transform: translate(-1%, 3%) rotate(-1deg); }
                75% { transform: translate(3%, 1%) rotate(0.5deg); }
            }

            .floating-circle {
                position: absolute;
                border-radius: 50%;
                background: rgba(255,255,255,0.07);
                animation: bobble 8s ease-in-out infinite;
            }
            @keyframes bobble {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
            }

            .branding-content {
                position: relative;
                z-index: 10;
                color: #fff;
                max-width: 420px;
            }
            .brand-logo-box {
                width: 72px; height: 72px;
                border-radius: 16px;
                background: rgba(255,255,255,0.15);
                backdrop-filter: blur(8px);
                border: 1px solid rgba(255,255,255,0.2);
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 2rem;
            }
            .brand-logo-box svg { width: 40px; height: 40px; }
            .brand-title {
                font-size: 2.25rem;
                font-weight: 800;
                margin-bottom: 1rem;
                line-height: 1.2;
            }
            .brand-title span { color: #a7f3d0; }
            .brand-desc {
                font-size: 1rem;
                color: #d1fae5;
                line-height: 1.7;
                margin-bottom: 2rem;
            }
            .feature-list { list-style: none; }
            .feature-item {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 1rem;
            }
            .feature-icon {
                width: 40px; height: 40px;
                min-width: 40px;
                border-radius: 12px;
                background: rgba(255,255,255,0.15);
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .feature-icon svg { width: 20px; height: 20px; }
            .feature-title { font-weight: 600; font-size: 0.875rem; }
            .feature-sub { font-size: 0.75rem; color: #a7f3d0; }

            /* ===== Right Form Panel ===== */
            .auth-form-panel {
                width: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2rem;
                position: relative;
                background: linear-gradient(135deg, #065f46 0%, #059669 50%, #34d399 100%);
            }
            @media (min-width: 1024px) {
                .auth-form-panel {
                    width: 50%;
                    background: #f0fdf4;
                }
            }

            .glass-card {
                background: rgba(255, 255, 255, 0.97);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255,255,255,0.4);
                border-radius: 24px;
                box-shadow: 0 25px 50px rgba(0,0,0,0.12);
                width: 100%;
                max-width: 420px;
                padding: 2.5rem;
            }

            /* Mobile logo */
            .mobile-logo {
                text-align: center;
                margin-bottom: 1.5rem;
            }
            @media (min-width: 1024px) {
                .mobile-logo { display: none; }
            }
            .mobile-logo-icon {
                width: 56px; height: 56px;
                border-radius: 16px;
                background: #059669;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 0.75rem;
            }
            .mobile-logo-icon svg { width: 32px; height: 32px; color: #fff; }
            .mobile-logo-text { font-size: 1.25rem; font-weight: 700; color: #1f2937; }

            /* Form Elements */
            .form-header h2 { font-size: 1.5rem; font-weight: 700; color: #1f2937; }
            .form-header p { font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem; }

            .form-group { margin-bottom: 1rem; }
            .form-label {
                display: block;
                font-size: 0.875rem;
                font-weight: 500;
                color: #374151;
                margin-bottom: 0.375rem;
            }
            .form-input {
                width: 100%;
                background: #f9fafb;
                border: 1.5px solid #e5e7eb;
                border-radius: 12px;
                padding: 0.75rem 1rem;
                font-size: 0.875rem;
                font-family: inherit;
                outline: none;
                transition: all 0.25s ease;
                color: #1f2937;
            }
            .form-input:focus {
                border-color: #059669;
                box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.15);
                background: #fff;
            }
            .form-input::placeholder { color: #9ca3af; }

            .form-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 1.5rem;
            }
            .remember-label {
                display: flex;
                align-items: center;
                gap: 8px;
                cursor: pointer;
                font-size: 0.875rem;
                color: #4b5563;
            }
            .remember-label input[type="checkbox"] {
                width: 16px; height: 16px;
                border-radius: 4px;
                accent-color: #059669;
            }
            .forgot-link {
                font-size: 0.875rem;
                color: #059669;
                text-decoration: none;
                font-weight: 500;
                transition: color 0.2s;
            }
            .forgot-link:hover { color: #047857; }

            .btn-submit {
                width: 100%;
                background: linear-gradient(135deg, #059669 0%, #047857 100%);
                color: #fff;
                font-weight: 600;
                font-size: 0.875rem;
                padding: 0.8rem 1.5rem;
                border-radius: 12px;
                border: none;
                cursor: pointer;
                transition: all 0.3s ease;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                font-family: inherit;
            }
            .btn-submit:hover {
                background: linear-gradient(135deg, #047857 0%, #065f46 100%);
                transform: translateY(-1px);
                box-shadow: 0 8px 25px rgba(5, 150, 105, 0.35);
            }
            .btn-submit:active { transform: translateY(0); }

            .alt-link {
                text-align: center;
                margin-top: 1.5rem;
                font-size: 0.875rem;
                color: #6b7280;
            }
            .alt-link a {
                color: #059669;
                text-decoration: none;
                font-weight: 600;
                transition: color 0.2s;
            }
            .alt-link a:hover { color: #047857; }

            .form-error {
                list-style: none;
                margin-top: 0.5rem;
                padding: 0;
            }
            .form-error li {
                font-size: 0.8rem;
                color: #dc2626;
            }

            .session-status {
                font-size: 0.875rem;
                color: #059669;
                font-weight: 500;
                margin-bottom: 1rem;
            }

            /* Footer */
            .auth-footer {
                position: absolute;
                bottom: 1.5rem;
                left: 0;
                width: 100%;
                text-align: center;
                font-size: 0.75rem;
                color: rgba(255,255,255,0.7);
            }
            @media (min-width: 1024px) {
                .auth-footer { color: #6b7280; }
            }
        </style>
    </head>
    <body>
        <div class="auth-wrapper">
            {{-- Floating decorative shapes --}}

            {{-- ===== Left Panel: Branding ===== --}}
            <div class="auth-branding">
                <div class="floating-circle" style="width:300px;height:300px;top:-100px;right:-80px;"></div>
                <div class="floating-circle" style="width:200px;height:200px;bottom:-60px;left:-60px;animation-delay:2s;"></div>
                <div class="floating-circle" style="width:150px;height:150px;top:40%;right:10%;animation-delay:4s;"></div>

                <div class="branding-content">
                    {{-- Logo Icon --}}
                    <div class="brand-logo-box">
                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
                        </svg>
                    </div>

                    <h1 class="brand-title">Ternate <span>Bersih</span></h1>
                    <p class="brand-desc">
                        Sistem Informasi Pelaporan dan Penanganan Sampah Berbasis GIS
                        untuk mewujudkan Kota Ternate yang bersih, sehat, dan berkelanjutan.
                    </p>

                    {{-- Feature highlights --}}
                    <ul class="feature-list">
                        <li class="feature-item">
                            <div class="feature-icon">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                </svg>
                            </div>
                            <div>
                                <div class="feature-title">Pelaporan Berbasis Lokasi</div>
                                <div class="feature-sub">Deteksi otomatis koordinat GPS</div>
                            </div>
                        </li>
                        <li class="feature-item">
                            <div class="feature-icon">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                                </svg>
                            </div>
                            <div>
                                <div class="feature-title">Pemetaan GIS Interaktif</div>
                                <div class="feature-sub">Heatmap & cluster wilayah rawan</div>
                            </div>
                        </li>
                        <li class="feature-item">
                            <div class="feature-icon">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <div class="feature-title">Respon Cepat</div>
                                <div class="feature-sub">SLA penanganan 1×24 jam</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- ===== Right Panel: Form ===== --}}
            <div class="auth-form-panel">
                <div class="glass-card">
                    {{-- Mobile logo --}}
                    <div class="mobile-logo">
                        <div class="mobile-logo-icon">
                            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
                            </svg>
                        </div>
                        <div class="mobile-logo-text">Ternate Bersih</div>
                    </div>

                    {{ $slot }}
                </div>

                {{-- Footer --}}
                <div class="auth-footer">
                    &copy; {{ date('Y') }} Dinas Lingkungan Hidup Kota Ternate &middot; SIPAS v1.0
                </div>
            </div>
        </div>
    </body>
</html>
