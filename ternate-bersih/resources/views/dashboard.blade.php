<x-app-layout>
    <x-slot name="header">
        <h2 class="text-sm font-bold" style="color: hsl(220 15% 13%);">Dashboard Administrator</h2>
    </x-slot>

    {{-- Welcome Card --}}
    <div class="rounded-xl overflow-hidden mb-5" style="background: white; border: 1px solid hsl(220 10% 90%); box-shadow: 0 1px 4px hsl(220 15% 13% / 0.04);">
        <div class="p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between relative overflow-hidden" style="border-left: 3px solid hsl(168 78% 21%);">
            <div class="mb-3 sm:mb-0">
                <h3 class="text-sm font-bold" style="color: hsl(220 15% 13%);">Selamat datang, <span style="color: hsl(168 78% 21%);">{{ Auth::user()->name }}</span></h3>
                <p class="text-xs mt-1 max-w-lg leading-relaxed" style="color: hsl(220 8% 55%);">Sistem Informasi Pelaporan dan Penanganan Sampah Berbasis GIS (SIPAS) Kota Ternate.</p>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-semibold" style="background: hsl(168 42% 92%); color: hsl(168 78% 21%); border: 1px solid hsl(168 42% 86%);">
                <span class="w-1.5 h-1.5 rounded-full mr-1.5" style="background: hsl(168 78% 40%);"></span> {{ Auth::user()->role }}
            </span>
        </div>
    </div>

    {{-- Top Level KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
        {{-- Total Laporan --}}
        <div class="rounded-xl p-4 relative overflow-hidden" style="background: white; border: 1px solid hsl(220 10% 90%); box-shadow: 0 1px 4px hsl(220 15% 13% / 0.04);">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-50 to-transparent rounded-bl-full opacity-50"></div>
            <div class="flex items-center gap-3 relative z-10">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: hsl(210 100% 95%); color: hsl(210 100% 40%);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-black leading-none" style="color: hsl(220 15% 13%);">{{ $totalReports }}</p>
                    <p class="text-[10px] font-bold mt-1 uppercase tracking-wider" style="color: hsl(220 8% 55%);">Total Laporan</p>
                </div>
            </div>
            <div class="mt-4 pt-3 flex items-center justify-between relative z-10 border-t" style="border-color: hsl(220 10% 92%);">
                <span class="text-[10px] font-semibold text-blue-600">Keseluruhan</span>
                <span class="text-[10px] font-semibold text-gray-400">Database Utama</span>
            </div>
        </div>

        {{-- Menunggu Verifikasi --}}
        <div class="rounded-xl p-4 relative overflow-hidden" style="background: white; border: 1px solid hsl(220 10% 90%); box-shadow: 0 1px 4px hsl(220 15% 13% / 0.04);">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-amber-50 to-transparent rounded-bl-full opacity-50"></div>
            <div class="flex items-center gap-3 relative z-10">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: hsl(45 93% 92%); color: hsl(45 93% 35%);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-black leading-none" style="color: hsl(220 15% 13%);">{{ $waitingVerification }}</p>
                    <p class="text-[10px] font-bold mt-1 uppercase tracking-wider" style="color: hsl(220 8% 55%);">Perlu Verifikasi</p>
                </div>
            </div>
            <div class="mt-4 pt-3 flex items-center justify-between relative z-10 border-t" style="border-color: hsl(220 10% 92%);">
                <span class="text-[10px] font-semibold text-amber-600">Laporan Baru Masuk</span>
                <a href="{{ route('admin.reports.verifications') }}" class="text-[10px] font-bold text-amber-700 hover:underline">Tinjau →</a>
            </div>
        </div>

        {{-- Sedang Ditangani --}}
        <div class="rounded-xl p-4 relative overflow-hidden" style="background: white; border: 1px solid hsl(220 10% 90%); box-shadow: 0 1px 4px hsl(220 15% 13% / 0.04);">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-indigo-50 to-transparent rounded-bl-full opacity-50"></div>
            <div class="flex items-center gap-3 relative z-10">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: hsl(226 100% 95%); color: hsl(226 100% 50%);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-black leading-none" style="color: hsl(220 15% 13%);">{{ $inProgress }}</p>
                    <p class="text-[10px] font-bold mt-1 uppercase tracking-wider" style="color: hsl(220 8% 55%);">Dalam Proses</p>
                </div>
            </div>
            <div class="mt-4 pt-3 flex items-center justify-between relative z-10 border-t" style="border-color: hsl(220 10% 92%);">
                <span class="text-[10px] font-semibold text-indigo-600">Armada Dikerahkan</span>
                <a href="{{ route('admin.reports.completions') }}" class="text-[10px] font-bold text-indigo-700 hover:underline">Pantau →</a>
            </div>
        </div>

        {{-- Selesai --}}
        <div class="rounded-xl p-4 relative overflow-hidden" style="background: white; border: 1px solid hsl(220 10% 90%); box-shadow: 0 1px 4px hsl(220 15% 13% / 0.04);">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-emerald-50 to-transparent rounded-bl-full opacity-50"></div>
            <div class="flex items-center gap-3 relative z-10">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: hsl(168 42% 92%); color: hsl(168 78% 21%);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-black leading-none" style="color: hsl(220 15% 13%);">{{ $completed }}</p>
                    <p class="text-[10px] font-bold mt-1 uppercase tracking-wider" style="color: hsl(220 8% 55%);">Selesai (Tuntas)</p>
                </div>
            </div>
            <div class="mt-4 pt-3 flex items-center justify-between relative z-10 border-t" style="border-color: hsl(220 10% 92%);">
                <span class="text-[10px] font-semibold text-emerald-600">Telah Dibersihkan</span>
                <a href="{{ route('reports.index') }}" class="text-[10px] font-bold text-emerald-700 hover:underline">Lihat Riwayat →</a>
            </div>
        </div>
    </div>

    {{-- Map Container --}}
    <div class="rounded-xl p-5 mb-5" style="background: white; border: 1px solid hsl(220 10% 90%); box-shadow: 0 1px 4px hsl(220 15% 13% / 0.04);">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xs font-bold uppercase tracking-wider" style="color: hsl(220 8% 55%);">Pemetaan Titik Sampah Terkini</h3>
            <span class="px-2 py-1 bg-emerald-50 text-emerald-600 rounded text-[9px] font-bold">Live Data GIS</span>
        </div>
        <div id="map" class="w-full rounded-lg" style="height: 400px; z-index: 10;"></div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        .leaflet-popup-content-wrapper { border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .leaflet-popup-content { font-family: 'Inter', sans-serif; }
    </style>
    @endpush

    @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi peta di koordinat Ternate
            var map = L.map('map').setView([0.7893, 127.3540], 13);

            // Tambahkan tile layer (CartoDB Positron untuk tampilan enterprise/bersih)
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                subdomains: 'abcd',
                maxZoom: 20
            }).addTo(map);

            // Data Titik Sampah dari Database
            var liveReports = @json($mapReports);

            // Icon kustom berdasarkan status
            var iconWarning = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-orange.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
            });

            var iconProcess = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
            });

            // Menyimpan semua marker untuk mengatur zoom otomatis
            var markers = [];
            var offsetMap = {}; // Penyimpanan koordinat untuk mencegah penumpukan

            // Render marker ke peta
            liveReports.forEach(function(report) {
                var icon = report.status === 'Menunggu Verifikasi' ? iconWarning : iconProcess;
                var badgeColor = report.status === 'Menunggu Verifikasi' ? 'background: #fef3c7; color: #d97706;' : 'background: #e0e7ff; color: #4338ca;';
                var reportUrl = "/reports/" + report.id;
                
                // Mencegah marker menumpuk jika dari lokasi yang sama persis
                var coordKey = report.lat.toFixed(5) + '-' + report.lng.toFixed(5);
                var latOffset = 0;
                var lngOffset = 0;

                if (offsetMap[coordKey]) {
                    // Tambahkan penyebaran kecil (jitter) secara acak (~15 meter)
                    latOffset = (Math.random() - 0.5) * 0.0003;
                    lngOffset = (Math.random() - 0.5) * 0.0003;
                    offsetMap[coordKey]++;
                } else {
                    offsetMap[coordKey] = 1;
                }

                var finalLat = report.lat + latOffset;
                var finalLng = report.lng + lngOffset;

                var popupContent = `
                    <div style="min-width: 150px;">
                        <h4 style="margin: 0 0 5px 0; font-weight: 700; font-size: 13px; color: #1e293b;">
                            <a href="${reportUrl}" style="text-decoration: none; color: inherit;" target="_blank">
                                ${report.report_number}
                            </a>
                        </h4>
                        <p style="margin: 0 0 8px 0; font-size: 11px; color: #64748b; font-weight: 500;">${report.address}</p>
                        <span style="display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 600; ${badgeColor}">
                            ${report.status}
                        </span>
                    </div>
                `;

                var marker = L.marker([finalLat, finalLng], { icon: icon }).bindPopup(popupContent);
                markers.push(marker);
            });

            if (markers.length > 0) {
                var group = L.featureGroup(markers).addTo(map);
                map.fitBounds(group.getBounds(), { padding: [50, 50] });
            }

            // ===== AUTO-REFRESH REALTIME =====
            var lastKnownTotal = {{ $totalReports }};

            setInterval(function() {
                fetch('/api/dashboard-stats', {
                    headers: { 'Accept': 'application/json' }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.totalReports > lastKnownTotal) {
                        // Ada laporan baru masuk!
                        lastKnownTotal = data.totalReports;
                        
                        // Tampilkan notifikasi visual
                        var notif = document.createElement('div');
                        notif.innerHTML = '🔔 Laporan baru masuk! Memuat ulang data...';
                        notif.style.cssText = 'position:fixed;top:20px;right:20px;background:#059669;color:white;padding:12px 20px;border-radius:12px;z-index:9999;font-size:13px;font-weight:600;box-shadow:0 4px 12px rgba(0,0,0,0.15);animation:fadeIn 0.3s ease';
                        document.body.appendChild(notif);

                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    }
                })
                .catch(function() { /* Abaikan error jaringan */ });
            }, 15000); // Cek setiap 15 detik
        });
    </script>
    @endpush
</x-app-layout>
