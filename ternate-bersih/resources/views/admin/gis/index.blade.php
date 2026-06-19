<x-app-layout>
    <x-slot name="header">
        <h2 class="text-sm font-bold" style="color: hsl(220 15% 13%);">Peta Sebaran Laporan (GIS)</h2>
    </x-slot>

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
        <style>
            #gis-map {
                height: 75vh;
                border-radius: 0.75rem;
                border: 1px solid hsl(220 10% 90%);
                box-shadow: 0 1px 4px hsl(220 15% 13% / 0.04);
            }
        </style>
    @endpush

    <div class="mb-4 bg-white p-4 rounded-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border" style="border-color: hsl(220 10% 90%);">
        <div>
            <h3 class="text-base font-bold" style="color: hsl(220 15% 13%);">Pemetaan Area Tumpukan Sampah</h3>
            <p class="text-xs" style="color: hsl(220 8% 55%);">Analisis geospasial penyebaran titik sampah berdasarkan laporan masyarakat.</p>
        </div>
        
        <form action="{{ route('admin.gis.index') }}" method="GET" class="flex items-center gap-2 w-full md:w-auto">
            <label for="status" class="text-xs font-bold text-gray-700">Filter Status:</label>
            <select name="status" id="status" onchange="this.form.submit()" class="text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 py-1.5 px-3">
                <option value="Semua" {{ $statusFilter == 'Semua' ? 'selected' : '' }}>Semua Laporan</option>
                <option value="Aktif" {{ $statusFilter == 'Aktif' ? 'selected' : '' }}>Hanya yang Aktif (Belum Selesai)</option>
                <option value="Menunggu Verifikasi" {{ $statusFilter == 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                <option value="Dalam Penanganan" {{ $statusFilter == 'Dalam Penanganan' ? 'selected' : '' }}>Dalam Penanganan</option>
                <option value="Selesai" {{ $statusFilter == 'Selesai' ? 'selected' : '' }}>Selesai Dibersihkan</option>
            </select>
        </form>
    </div>

    {{-- Container Peta --}}
    <div id="gis-map" class="relative z-0"></div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        {{-- Plugin Heatmap --}}
        <script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var map = L.map('gis-map').setView([0.7893, 127.3540], 13);

                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                    subdomains: 'abcd',
                    maxZoom: 20
                }).addTo(map);

                var reports = @json($reports);
                var markers = [];
                var heatData = [];

                // Definisi Ikon
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
                
                var iconSuccess = L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
                });

                var iconDanger = L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
                });

                reports.forEach(function(report) {
                    // Penentuan icon berdasarkan status
                    var icon = iconWarning; // Default
                    if (['Diverifikasi', 'Ditugaskan', 'Dalam Penanganan'].includes(report.status)) icon = iconProcess;
                    if (report.status === 'Selesai') icon = iconSuccess;
                    if (report.status === 'Ditolak') icon = iconDanger;

                    var badgeColor = 'background: #f3f4f6; color: #374151;';
                    if (report.status === 'Menunggu Verifikasi') badgeColor = 'background: #fef3c7; color: #d97706;';
                    if (['Diverifikasi', 'Ditugaskan', 'Dalam Penanganan'].includes(report.status)) badgeColor = 'background: #e0e7ff; color: #4338ca;';
                    if (report.status === 'Selesai') badgeColor = 'background: #d1fae5; color: #059669;';

                    var reportUrl = "/reports/" + report.id;
                    
                    var popupContent = `
                        <div style="min-width: 160px;">
                            <h4 style="margin: 0 0 5px 0; font-weight: 700; font-size: 13px; color: #1e293b;">
                                <a href="${reportUrl}" style="text-decoration: none; color: inherit;" target="_blank">
                                    ${report.report_number}
                                </a>
                            </h4>
                            <p style="margin: 0 0 8px 0; font-size: 11px; color: #64748b; font-weight: 500;">${report.address}</p>
                            <div style="display: flex; gap: 5px; align-items: center;">
                                <span style="display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 600; ${badgeColor}">
                                    ${report.status}
                                </span>
                                <span style="font-size: 10px; font-weight: bold; color: ${report.priority === 'Tinggi' ? '#ef4444' : '#64748b'};">
                                    Prioritas: ${report.priority}
                                </span>
                            </div>
                        </div>
                    `;

                    var marker = L.marker([report.lat, report.lng], { icon: icon }).bindPopup(popupContent);
                    markers.push(marker);

                    // Intensitas Heatmap berdasarkan prioritas (Tinggi = lebih panas)
                    var intensity = report.priority === 'Tinggi' ? 1.0 : (report.priority === 'Sedang' ? 0.6 : 0.3);
                    heatData.push([report.lat, report.lng, intensity]);
                });

                // Tampilkan Marker Group
                var markerGroup = L.featureGroup(markers).addTo(map);

                // Jika ada data, zoom otomatis ke area yang memiliki pin
                if (markers.length > 0) {
                    map.fitBounds(markerGroup.getBounds(), { padding: [50, 50] });
                }

                // Tambahkan layer Heatmap (Peta Panas) sebagai latar belakang blur
                var heat = L.heatLayer(heatData, {
                    radius: 25,
                    blur: 15,
                    maxZoom: 14,
                    gradient: {0.4: 'blue', 0.6: 'lime', 0.8: 'yellow', 1.0: 'red'}
                }).addTo(map);
            });
        </script>
    @endpush
</x-app-layout>
