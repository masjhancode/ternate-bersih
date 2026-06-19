<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Laporan #{{ $report->id }} - SIPAS Ternate</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.6);
        }
    </style>
</head>
<body class="text-slate-800 antialiased">
    <!-- Navigation -->
    <nav class="fixed w-full z-50 glass-nav transition-all duration-300">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-teal-600 flex items-center justify-center shadow-lg shadow-teal-500/30 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </div>
                    <span class="font-bold text-lg tracking-tight text-slate-900 group-hover:text-teal-700 transition-colors">Kembali ke Beranda</span>
                </a>
            </div>
        </div>
    </nav>

    <main class="pt-32 pb-20 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white border border-slate-200 shadow-sm rounded-3xl overflow-hidden">
            <!-- Header Image -->
            <div class="h-64 sm:h-96 w-full bg-slate-200 relative">
                @if($report->photo_path)
                    <img src="{{ Storage::url($report->photo_path) }}" alt="Foto Laporan" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center bg-slate-100 text-slate-400">
                        <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>Tidak ada foto lampiran</span>
                    </div>
                @endif
                
                <!-- Status Badge -->
                <div class="absolute top-6 left-6">
                    @if($report->status === 'Menunggu')
                        <span class="px-4 py-1.5 bg-amber-500 text-white text-sm font-bold tracking-wide rounded-full shadow-lg">MENUNGGU RESPON</span>
                    @elseif($report->status === 'Diverifikasi')
                        <span class="px-4 py-1.5 bg-blue-500 text-white text-sm font-bold tracking-wide rounded-full shadow-lg">SEDANG DIPROSES</span>
                    @elseif($report->status === 'Selesai')
                        <span class="px-4 py-1.5 bg-emerald-500 text-white text-sm font-bold tracking-wide rounded-full shadow-lg">SELESAI DITANGANI</span>
                    @else
                        <span class="px-4 py-1.5 bg-red-500 text-white text-sm font-bold tracking-wide rounded-full shadow-lg">DITOLAK</span>
                    @endif
                </div>
            </div>

            <div class="p-8 md:p-12">
                <!-- Meta Info -->
                <div class="flex flex-wrap items-center gap-4 mb-6">
                    <span class="px-3 py-1 bg-slate-100 text-slate-700 text-sm font-semibold rounded-lg border border-slate-200">
                        Kategori: {{ $report->category->name ?? 'Umum' }}
                    </span>
                    <span class="text-sm text-slate-500 flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Dilaporkan {{ $report->created_at->translatedFormat('l, d F Y - H:i') }}
                    </span>
                </div>

                <h1 class="text-2xl md:text-3xl font-bold text-slate-900 mb-4 leading-snug">
                    {{ $report->description }}
                </h1>
                
                <div class="flex items-start mt-6 p-4 bg-slate-50 rounded-xl border border-slate-100">
                    <svg class="w-6 h-6 text-teal-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <div class="ml-3">
                        <h4 class="text-sm font-bold text-slate-900">Lokasi Tumpukan:</h4>
                        <p class="text-slate-600 mt-1 leading-relaxed">{{ $report->address }}</p>
                    </div>
                </div>

                <hr class="my-10 border-slate-200">

                <h3 class="text-xl font-bold text-slate-900 mb-8">Riwayat Penanganan</h3>
                
                @if($report->progresses->count() > 0)
                    <div class="relative border-l-2 border-slate-200 ml-3 md:ml-4 space-y-8">
                        @foreach($report->progresses as $progress)
                            <div class="relative pl-8">
                                <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-white border-2 border-teal-500"></div>
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-2 gap-2">
                                    <h4 class="text-base font-bold text-slate-800">{{ $progress->status }}</h4>
                                    <span class="text-sm text-slate-500 bg-slate-100 px-2 py-0.5 rounded">{{ $progress->created_at->translatedFormat('d M Y, H:i') }}</span>
                                </div>
                                <p class="text-slate-600 text-sm leading-relaxed bg-slate-50 p-3 rounded-lg border border-slate-100">
                                    {{ $progress->notes ?? 'Pembaruan status sistem' }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 bg-slate-50 rounded-xl border border-dashed border-slate-300">
                        <p class="text-slate-500">Belum ada riwayat penanganan (Laporan baru masuk).</p>
                    </div>
                @endif
            </div>
        </div>
    </main>
</body>
</html>
