<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Ternate Bersih') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-label { padding: 0 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: hsl(220 8% 55%); }
        .sidebar-link { display: flex; align-items: center; gap: 10px; padding: 7px 12px; font-size: 13px; font-weight: 500; border-radius: 8px; transition: all 0.15s; color: hsl(220 8% 42%); }
        .sidebar-link:hover { background: hsl(168 42% 94%); color: hsl(168 78% 18%); }
        .sidebar-link.active { background: hsl(168 42% 92%); color: hsl(168 78% 21%); font-weight: 600; }
        .sidebar-link svg { width: 18px; height: 18px; flex-shrink: 0; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: hsl(220 10% 85%); border-radius: 99px; }
    </style>
    @stack('styles')
</head>
<body class="antialiased" style="background: hsl(210 20% 97%);" x-data="{ sidebarOpen: false }">
    <div class="flex min-h-screen">

        {{-- Mobile overlay --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-gray-900/40 backdrop-blur-sm md:hidden" style="display:none;"></div>

        {{-- =================== SIDEBAR =================== --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
            class="fixed left-0 top-0 h-full w-60 z-40 flex flex-col transition-transform duration-300 ease-in-out"
            style="background: hsl(0 0% 99%); border-right: 1px solid hsl(220 10% 90%); box-shadow: 4px 0 24px hsl(220 15% 13% / 0.04);">

            {{-- Logo --}}
            <div class="h-14 flex items-center px-4 border-b flex-shrink-0" style="border-color: hsl(220 10% 90%);">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shadow-sm transition-transform group-hover:scale-105" style="background: linear-gradient(135deg, hsl(168 78% 21%), hsl(168 74% 14%));">
                        <svg class="w-4 h-4 text-white" style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418"/></svg>
                    </div>
                    <div class="leading-none">
                        <span class="block text-sm font-bold" style="color: hsl(168 78% 21%);">Ternate Bersih</span>
                        <span class="block text-[10px] font-medium" style="color: hsl(220 8% 55%);">
                            {{ Auth::user()->role }}
                        </span>
                    </div>
                </a>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5 custom-scrollbar">

                <div class="sidebar-label mb-2">Menu Utama</div>
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('admin.gis.index') }}" class="sidebar-link {{ request()->routeIs('admin.gis.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    <span>Peta Sebaran GIS</span>
                </a>

                <div class="sidebar-label mt-5 mb-2">Manajemen Laporan</div>
                <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Semua Laporan</span>
                </a>
                <a href="{{ route('admin.reports.verifications') }}" class="sidebar-link {{ request()->routeIs('admin.reports.verifications') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span>Verifikasi Laporan</span>
                </a>
                <a href="{{ route('admin.reports.assignments') }}" class="sidebar-link {{ request()->routeIs('admin.reports.assignments') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    <span>Penugasan Armada</span>
                </a>
                <a href="{{ route('admin.reports.completions') }}" class="sidebar-link {{ request()->routeIs('admin.reports.completions') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Penyelesaian Laporan</span>
                </a>

                <div class="sidebar-label mt-5 mb-2">Master Data</div>
                <a href="{{ route('admin.regions.index') }}" class="sidebar-link {{ request()->routeIs('admin.regions.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>Data Wilayah (Kec/Kel)</span>
                </a>
                <a href="{{ route('admin.fleets.index') }}" class="sidebar-link {{ request()->routeIs('admin.fleets.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    <span>Armada & Driver</span>
                </a>
                <a href="{{ route('categories.index') }}" class="sidebar-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    <span>Kategori Sampah</span>
                </a>

                <div class="sidebar-label mt-5 mb-2">Sistem & Keamanan</div>
                <a href="{{ route('admin.exports.index') }}" class="sidebar-link {{ request()->routeIs('admin.exports.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Laporan & Ekspor</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Manajemen Pengguna</span>
                </a>
            </nav>

            {{-- User Profile Footer --}}
            <div class="p-3 border-t flex-shrink-0" style="border-color: hsl(220 10% 90%); background: hsl(210 20% 98%);">
                <div class="flex items-center gap-2.5 mb-2.5">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold text-white flex-shrink-0" style="background: linear-gradient(135deg, hsl(168 78% 21%), hsl(168 74% 14%));">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold truncate" style="color: hsl(220 15% 13%);">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] truncate uppercase" style="color: hsl(220 8% 55%);">{{ Auth::user()->role }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors border" style="border-color: hsl(0 84% 90%); color: hsl(0 84% 50%); background: white;" onmouseover="this.style.backgroundColor='hsl(0 84% 98%)';" onmouseout="this.style.backgroundColor='white';">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- =================== MAIN CONTENT =================== --}}
        <div class="flex-1 min-w-0 md:ml-60 flex flex-col min-h-screen">

            {{-- Topbar --}}
            <header class="h-14 flex items-center justify-between px-4 sm:px-5 sticky top-0 z-30 flex-shrink-0" style="background: rgba(255,255,255,0.92); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-bottom: 1px solid hsl(220 10% 90%); box-shadow: 0 1px 8px hsl(220 15% 13% / 0.04);">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden w-8 h-8 flex items-center justify-center rounded-lg transition-colors" style="color: hsl(220 8% 45%);" onmouseover="this.style.background='hsl(220 10% 93%)';" onmouseout="this.style.background='';">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    @isset($header)
                        <div class="hidden sm:block">{{ $header }}</div>
                    @endisset
                    <div class="hidden sm:flex items-center gap-1.5 ml-2">
                        <span class="flex h-1.5 w-1.5 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                        </span>
                        <span class="text-[10px] font-medium uppercase" style="color: hsl(220 8% 60%);">
                            Sistem Aktif
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-2 px-2.5 py-1 rounded-lg" style="background: hsl(168 42% 92%);">
                        <div class="w-6 h-6 rounded-md flex items-center justify-center text-[10px] font-bold text-white" style="background: hsl(168 78% 21%);">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <span class="text-xs font-semibold hidden sm:block max-w-[100px] truncate" style="color: hsl(168 74% 11%);">{{ Auth::user()->name }}</span>
                    </div>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 p-4 md:p-6">
                @isset($header)
                    <div class="sm:hidden mb-4">{{ $header }}</div>
                @endisset
                {{ $slot }}
            </main>

            {{-- Footer --}}
            <footer class="border-t px-4 py-3" style="border-color: hsl(220 10% 90%); background: white;">
                <p class="text-[10px] text-center" style="color: hsl(220 8% 60%);">&copy; {{ date('Y') }} Ternate Bersih · Dinas Lingkungan Hidup Kota Ternate</p>
            </footer>
        </div>
    </div>
    @stack('scripts')
    
    <!-- Real-time Watcher (Backend Auto-Refresh) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentChecksum = null;
            
            // Cek perubahan data setiap 3 detik
            setInterval(() => {
                // Hanya refresh jika user TIDAK sedang berinteraksi dengan form input
                const activeTag = document.activeElement ? document.activeElement.tagName : '';
                if (activeTag === 'INPUT' || activeTag === 'TEXTAREA' || activeTag === 'SELECT') {
                    return; // Lewati pengecekan jika admin sedang mengetik/mengisi form
                }

                fetch('/api/dashboard-stats')
                    .then(res => res.json())
                    .then(data => {
                        if (currentChecksum === null) {
                            currentChecksum = data.checksum;
                        } else if (currentChecksum !== data.checksum) {
                            // Ada data baru/perubahan status (Masyarakat lapor, Driver selesai, dll)
                            // Munculkan indikator sinkronisasi lalu reload
                            let syncIndicator = document.createElement('div');
                            syncIndicator.innerHTML = '<div style="position:fixed; bottom:20px; right:20px; background:#0D9488; color:white; padding:10px 20px; border-radius:8px; font-size:12px; font-weight:bold; box-shadow:0 4px 12px rgba(0,0,0,0.1); z-index:9999; display:flex; align-items:center; gap:8px;"><span style="width:10px; height:10px; border:2px solid white; border-top-color:transparent; border-radius:50%; animation:spin 1s linear infinite;"></span> Data Real-time Diperbarui...</div><style>@keyframes spin { 100% { transform:rotate(360deg); } }</style>';
                            document.body.appendChild(syncIndicator);
                            
                            setTimeout(() => {
                                window.location.reload();
                            }, 800);
                        }
                    })
                    .catch(err => console.log('Realtime sync paused', err));
            }, 3000);
        });
    </script>
</body>
</html>
