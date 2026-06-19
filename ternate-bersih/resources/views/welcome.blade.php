<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIPAS - Sistem Informasi Pelaporan Sampah Kota Ternate</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts / Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.6);
        }
        .hero-pattern {
            background-color: #0D9488;
            background-image: radial-gradient(circle at 20% 150%, #115E59 0%, transparent 50%),
                              radial-gradient(circle at 80% -50%, #2DD4BF 0%, transparent 50%);
        }
        .premium-shadow {
            box-shadow: 0 20px 40px -15px rgba(13, 148, 136, 0.15);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-teal-500 selection:text-white">

    <!-- Navigation -->
    <nav class="fixed w-full z-50 glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-teal-600 flex items-center justify-center shadow-lg shadow-teal-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="font-bold text-xl tracking-tight text-slate-900">Ternate<span class="text-teal-600">Bersih</span></span>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#beranda" class="text-sm font-medium text-slate-600 hover:text-teal-600 transition-colors">Beranda</a>
                    <a href="#statistik" class="text-sm font-medium text-slate-600 hover:text-teal-600 transition-colors">Statistik Publik</a>
                    <a href="#laporan" class="text-sm font-medium text-slate-600 hover:text-teal-600 transition-colors">Laporan Terbaru</a>
                </div>

                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm font-semibold text-teal-700 bg-teal-50 hover:bg-teal-100 px-5 py-2.5 rounded-full transition-all duration-200">
                            Masuk Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-teal-600 hidden sm:block">Log in</a>
                        @if (Route::has('register'))
                            <!-- Tombol Daftar dinonaktifkan atas permintaan (Hidden) -->
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="beranda" class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden hero-pattern">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 mix-blend-overlay"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <span class="inline-block py-1.5 px-4 rounded-full bg-teal-500/20 border border-teal-400/30 text-teal-50 text-sm font-semibold mb-6 backdrop-blur-sm">
                Inisiatif Pemerintah Kota Ternate
            </span>
            <h1 class="text-4xl md:text-5xl lg:text-7xl font-extrabold text-white tracking-tight mb-8 leading-tight">
                Mari Wujudkan Ternate <br class="hidden md:block" />
                <span class="text-teal-200">Bebas Sampah Liar</span>
            </h1>
            <p class="mt-4 text-lg md:text-xl text-teal-50 max-w-2xl mx-auto mb-10 font-light leading-relaxed">
                Platform digital cerdas terpadu untuk melaporkan, melacak, dan menuntaskan permasalahan tumpukan sampah di lingkungan Anda secara real-time.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="javascript:alert('Pendaftaran akun dan pelaporan hanya dapat dilakukan melalui Aplikasi Mobile Ternate Bersih.');" class="inline-flex justify-center items-center px-8 py-4 text-base font-semibold text-teal-900 bg-white hover:bg-slate-50 rounded-full shadow-xl transition-all duration-300 transform hover:scale-105">
                    Lapor Sekarang
                    <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
                <a href="#laporan" class="inline-flex justify-center items-center px-8 py-4 text-base font-semibold text-white border border-teal-400/30 bg-teal-800/30 hover:bg-teal-800/50 backdrop-blur-md rounded-full transition-all duration-300">
                    Lihat Kinerja Kami
                </a>
            </div>
        </div>
        
        <!-- Decorative blobs -->
        <div class="absolute top-1/2 left-10 w-72 h-72 bg-teal-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
        <div class="absolute top-1/2 right-10 w-72 h-72 bg-emerald-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
    </section>

    <!-- Statistics Section -->
    <section id="statistik" class="py-16 bg-white relative z-20 -mt-8 rounded-t-3xl shadow-sm border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Stat 1 -->
                <div class="bg-white rounded-xl p-6 border border-slate-200 shadow-sm text-center flex flex-col items-center justify-center group hover:border-teal-300 transition-all duration-300">
                    <h3 class="text-3xl font-bold text-slate-800 mb-1">{{ number_format($stats['total_reports']) }}</h3>
                    <p class="text-slate-500 text-sm font-medium tracking-wide">Total Laporan Masuk</p>
                </div>
                
                <!-- Stat 2 -->
                <div class="bg-white rounded-xl p-6 border border-slate-200 shadow-sm text-center flex flex-col items-center justify-center group hover:border-teal-300 transition-all duration-300">
                    <h3 class="text-3xl font-bold text-slate-800 mb-1">{{ number_format($stats['completed_reports']) }}</h3>
                    <p class="text-slate-500 text-sm font-medium tracking-wide">Laporan Terselesaikan</p>
                </div>

                <!-- Stat 3 -->
                <div class="bg-white rounded-xl p-6 border border-slate-200 shadow-sm text-center flex flex-col items-center justify-center group hover:border-teal-300 transition-all duration-300">
                    <h3 class="text-3xl font-bold text-slate-800 mb-1">{{ number_format($stats['fleets_active']) }}</h3>
                    <p class="text-slate-500 text-sm font-medium tracking-wide">Armada Aktif</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Analytics Chart Section -->
    <section id="analitik" class="py-12 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl p-6 md:p-8 border border-slate-200 shadow-sm">
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Tren Pelaporan Sampah</h2>
                        <p class="text-slate-500 text-sm mt-1">Partisipasi pelaporan oleh masyarakat Ternate</p>
                    </div>
                    <div class="flex bg-slate-50 p-1 rounded-md border border-slate-200 self-start md:self-auto">
                        <button onclick="updateChart('daily')" id="btn-daily" class="chart-filter-btn active px-3 py-1.5 text-xs font-semibold rounded transition-all duration-200 bg-white shadow-sm text-teal-600 border border-slate-200">Harian</button>
                        <button onclick="updateChart('monthly')" id="btn-monthly" class="chart-filter-btn px-3 py-1.5 text-xs font-medium rounded transition-all duration-200 text-slate-500 hover:text-slate-800 border border-transparent">Bulanan</button>
                        <button onclick="updateChart('yearly')" id="btn-yearly" class="chart-filter-btn px-3 py-1.5 text-xs font-medium rounded transition-all duration-200 text-slate-500 hover:text-slate-800 border border-transparent">Tahunan</button>
                    </div>
                </div>
                
                <div class="relative w-full h-[300px]">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Reports Section -->
    <section id="laporan" class="py-24 bg-slate-100 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-12">
                <div>
                    <span class="text-teal-600 font-bold tracking-wider uppercase text-sm">Transparansi</span>
                    <h2 class="text-3xl font-bold text-slate-900 mt-2">Laporan Publik Terbaru</h2>
                </div>
                <a href="javascript:alert('Pelaporan hanya dapat dilakukan melalui Aplikasi Mobile Ternate Bersih.');" class="hidden sm:flex items-center text-teal-600 font-semibold hover:text-teal-700">
                    Lapor Sekarang <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($recent_reports as $report)
                    <a href="{{ route('public.report.show', $report->id) }}" class="bg-white rounded-2xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-lg hover:border-teal-200 transition-all duration-300 flex flex-col h-full group">
                        <div class="h-48 bg-slate-200 relative overflow-hidden">
                            @if($report->photo_path)
                                <img src="{{ Storage::url($report->photo_path) }}" alt="Foto Laporan" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-slate-100">
                                    <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                            <!-- Status Badge -->
                            <div class="absolute top-4 left-4">
                                @if($report->status === 'Menunggu')
                                    <span class="px-3 py-1 bg-amber-500 text-white text-xs font-bold rounded-full shadow-md">MENUNGGU</span>
                                @elseif($report->status === 'Diverifikasi')
                                    <span class="px-3 py-1 bg-blue-500 text-white text-xs font-bold rounded-full shadow-md">DIPROSES</span>
                                @elseif($report->status === 'Selesai')
                                    <span class="px-3 py-1 bg-emerald-500 text-white text-xs font-bold rounded-full shadow-md">SELESAI</span>
                                @else
                                    <span class="px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-full shadow-md">DITOLAK</span>
                                @endif
                            </div>
                        </div>
                        <div class="p-6 flex-1 flex flex-col">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-600 text-xs font-semibold rounded-md border border-slate-200">
                                    {{ $report->category->name ?? 'Umum' }}
                                </span>
                                <span class="text-xs text-slate-400 font-medium flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ $report->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-2 line-clamp-2">
                                {{ $report->description }}
                            </h3>
                            <div class="mt-auto pt-4 flex items-center text-sm text-slate-500">
                                <svg class="w-4 h-4 mr-1.5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span class="truncate">{{ $report->address }}</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-1 md:col-span-2 lg:col-span-3 py-16 text-center bg-white rounded-3xl border border-dashed border-slate-300">
                        <div class="inline-flex w-16 h-16 bg-slate-50 rounded-full items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">Belum Ada Laporan</h3>
                        <p class="text-slate-500 mt-2">Jadilah yang pertama untuk melaporkan tumpukan sampah di sekitar Anda.</p>
                    </div>
                @endforelse
            </div>
            
            <div class="mt-12 text-center sm:hidden">
                <a href="javascript:alert('Silakan unduh Aplikasi Mobile Ternate Bersih untuk mulai melapor.');" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-full text-white bg-teal-600 hover:bg-teal-700">
                    Mulai Melapor
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 border-t border-slate-800">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center gap-3 mb-6 md:mb-0">
                    <div class="w-8 h-8 rounded-lg bg-teal-600 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="font-bold text-xl tracking-tight text-white">Ternate<span class="text-teal-500">Bersih</span></span>
                </div>
                
                <div class="flex space-x-6">
                    <a href="#" class="text-slate-400 hover:text-white transition-colors">Tentang Kami</a>
                    <a href="#" class="text-slate-400 hover:text-white transition-colors">Kebijakan Privasi</a>
                    <a href="#" class="text-slate-400 hover:text-white transition-colors">Syarat & Ketentuan</a>
                </div>
            </div>
            <div class="mt-8 border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-base text-slate-400">
                    &copy; {{ date('Y') }} Dinas Lingkungan Hidup Kota Ternate. All rights reserved.
                </p>
                <p class="text-sm text-slate-500 mt-4 md:mt-0 flex items-center">
                    Dibuat dengan <svg class="w-4 h-4 mx-1 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path></svg> untuk Ternate yang lebih baik
                </p>
            </div>
        </div>
    </footer>

    <!-- Chart.js and Init Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        let myChart;
        const rawData = {
            daily: @json($daily_trends),
            monthly: @json($monthly_trends),
            yearly: @json($yearly_trends)
        };

        function getChartData(period) {
            const data = rawData[period] || [];
            return {
                labels: data.map(item => item.period),
                counts: data.map(item => item.count)
            };
        }

        function updateChart(period) {
            // Update button styles
            document.querySelectorAll('.chart-filter-btn').forEach(btn => {
                btn.classList.remove('bg-white', 'shadow-sm', 'text-teal-600', 'border-slate-200');
                btn.classList.add('text-slate-500', 'hover:text-slate-800', 'border-transparent');
            });
            const activeBtn = document.getElementById('btn-' + period);
            activeBtn.classList.remove('text-slate-500', 'hover:text-slate-800', 'border-transparent');
            activeBtn.classList.add('bg-white', 'shadow-sm', 'text-teal-600', 'border-slate-200');

            // Get new data
            const newData = getChartData(period);
            
            // Update chart
            myChart.data.labels = newData.labels;
            myChart.data.datasets[0].data = newData.counts;
            myChart.update();
        }

        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('trendChart').getContext('2d');
            
            // Create a teal gradient for the bars
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, '#0D9488'); // Teal-600
            gradient.addColorStop(1, '#2DD4BF'); // Teal-400
            
            const initialData = getChartData('daily'); // Default to Daily
            
            myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: initialData.labels,
                    datasets: [{
                        label: 'Total Laporan Sampah',
                        data: initialData.counts,
                        backgroundColor: gradient,
                        hoverBackgroundColor: '#0F766E', // Teal-700
                        borderRadius: 6,
                        borderSkipped: false,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1E293B',
                            padding: 12,
                            titleFont: { size: 14, family: "'Inter', sans-serif" },
                            bodyFont: { size: 14, family: "'Inter', sans-serif" },
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + ' Laporan Masyarakat';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#F1F5F9', drawBorder: false },
                            ticks: { color: '#64748B', font: { family: "'Inter', sans-serif" }, stepSize: 1 }
                        },
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: { color: '#64748B', font: { family: "'Inter', sans-serif" } }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
