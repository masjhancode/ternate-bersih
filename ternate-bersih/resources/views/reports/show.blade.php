<x-app-layout>
    <x-slot name="header">
        <h2 class="text-sm font-bold" style="color: hsl(220 15% 13%);">Detail Laporan: {{ $report->report_number }}</h2>
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold hover:underline" style="color: hsl(168 78% 21%);">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Riwayat Laporan
        </a>
    </div>

    @php
        $badgeStyle = 'background: hsl(45 93% 92%); color: hsl(45 93% 35%);'; // Menunggu Verifikasi
        if ($report->status == 'Selesai') $badgeStyle = 'background: hsl(168 42% 92%); color: hsl(168 78% 21%);';
        elseif (in_array($report->status, ['Dalam Penanganan', 'Ditugaskan', 'Diverifikasi'])) $badgeStyle = 'background: hsl(226 100% 95%); color: hsl(226 100% 50%);';
        elseif ($report->status == 'Ditolak') $badgeStyle = 'background: hsl(0 84% 95%); color: hsl(0 84% 50%);';
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- KOLOM KIRI: FOTO & DETAIL INFORMASI LAPORAN --}}
        <div class="space-y-6">
            
            {{-- Foto Laporan --}}
            <div class="rounded-xl overflow-hidden bg-white" style="border: 1px solid hsl(220 10% 90%); box-shadow: 0 1px 4px hsl(220 15% 13% / 0.04);">
                <div class="relative bg-gray-100 flex justify-center border-b h-64 sm:h-80" style="border-color: hsl(220 10% 90%);">
                    <img src="{{ Storage::url($report->photo_path) }}" alt="Foto Laporan" class="w-full h-full object-cover">
                </div>
            </div>

            {{-- Detail Laporan & Alamat --}}
            <div class="rounded-xl overflow-hidden bg-white" style="border: 1px solid hsl(220 10% 90%); box-shadow: 0 1px 4px hsl(220 15% 13% / 0.04);">
                <div class="p-5 md:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between mb-4 gap-3 sm:gap-4">
                        <div>
                            <h3 class="text-lg sm:text-xl font-black mb-1 leading-tight" style="color: hsl(220 15% 13%);">{{ $report->report_number }}</h3>
                            <p class="text-[10px] sm:text-xs font-semibold" style="color: hsl(220 8% 55%);">Tanggal Dilaporkan: {{ $report->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <span class="inline-block px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm whitespace-nowrap self-start" style="{{ $badgeStyle }}">
                            {{ $report->status }}
                        </span>
                    </div>

                    <div class="mb-5 p-4 rounded-lg bg-emerald-50 border border-emerald-100 flex gap-3 items-start">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-emerald-600 mb-1">Alamat Lengkap / Patokan</p>
                            <p class="text-sm font-semibold text-emerald-900 leading-snug">{{ $report->address }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-5">
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Kategori Sampah</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $report->category->name }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Prioritas Penanganan</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $report->priority }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Pelapor</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $report->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Target SLA</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $report->category->sla_hours }} Jam</p>
                        </div>
                    </div>

                    <div class="pt-4 border-t" style="border-color: hsl(220 10% 90%);">
                        <p class="text-[10px] uppercase font-bold text-gray-400 mb-1.5">Deskripsi Laporan</p>
                        <p class="text-sm leading-relaxed" style="color: hsl(220 15% 30%);">{{ $report->description ?: 'Tidak ada deskripsi tambahan.' }}</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN: PETA & TIMELINE RIWAYAT --}}
        <div class="space-y-6">
            
            {{-- Peta Lokasi --}}
            <div class="rounded-xl overflow-hidden bg-white" style="border: 1px solid hsl(220 10% 90%); box-shadow: 0 1px 4px hsl(220 15% 13% / 0.04);">
                <div class="p-4 border-b" style="border-color: hsl(220 10% 90%);">
                    <h3 class="text-xs font-bold uppercase tracking-wider" style="color: hsl(220 8% 55%);">Lokasi Laporan</h3>
                </div>
                <div id="detail-map" class="w-full bg-gray-100" style="height: 300px; z-index: 10;"></div>
                <div class="p-3 bg-gray-50 border-t flex justify-between items-center" style="border-color: hsl(220 10% 92%);">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-[10px] font-mono font-semibold text-gray-600">{{ number_format($report->lat, 6) }}, {{ number_format($report->lng, 6) }}</span>
                    </div>
                    <a href="https://maps.google.com/?q={{ $report->lat }},{{ $report->lng }}" target="_blank" class="text-[10px] px-2 py-1 bg-blue-100 rounded font-bold text-blue-700 hover:bg-blue-200 transition-colors">Buka di Maps →</a>
                </div>
            </div>

            {{-- Timeline Riwayat Penanganan --}}
            <div class="rounded-xl overflow-hidden bg-white" style="border: 1px solid hsl(220 10% 90%); box-shadow: 0 1px 4px hsl(220 15% 13% / 0.04);">
                <div class="p-4 border-b" style="border-color: hsl(220 10% 90%);">
                    <h3 class="text-xs font-bold uppercase tracking-wider" style="color: hsl(220 8% 55%);">Riwayat Penanganan (Timeline)</h3>
                </div>
                <div class="p-5">
                    @if($report->progresses->count() > 0)
                        <div class="space-y-4">
                            @foreach($report->progresses as $progress)
                            <div class="relative">
                                @if(!$loop->last)
                                <div class="absolute left-2.5 top-5 bottom-[-20px] w-0.5 bg-gray-200"></div>
                                @endif
                                <div class="relative z-10 flex gap-3">
                                    <div class="w-5 h-5 rounded-full mt-0.5 flex-shrink-0 flex items-center justify-center border-2 border-white shadow-sm" style="background: hsl(168 78% 40%);">
                                        <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-800">{{ $progress->status }}</p>
                                        <p class="text-[10px] text-gray-500 mt-0.5">{{ $progress->created_at->format('d M Y, H:i') }}</p>
                                        @if($progress->notes)
                                            <p class="text-[10px] mt-1.5 text-gray-600 bg-gray-50 p-2 rounded">{{ $progress->notes }}</p>
                                        @endif
                                        @if($progress->fleet)
                                            <p class="text-[9px] mt-1 font-bold text-emerald-600">Armada: {{ $progress->fleet->plate_number }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6">
                            <svg class="mx-auto h-10 w-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-xs font-semibold text-gray-500">Belum ada riwayat penanganan.</p>
                            <p class="text-[10px] text-gray-400 mt-1">Laporan masih menunggu verifikasi petugas.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    @endpush

    @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var lat = {{ $report->lat }};
            var lng = {{ $report->lng }};
            var status = "{{ $report->status }}";
            
            var map = L.map('detail-map').setView([lat, lng], 15);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap',
                maxZoom: 20
            }).addTo(map);

            // Set marker color based on status
            var iconUrl = 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-orange.png';
            if (status === 'Selesai') iconUrl = 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png';
            else if (status === 'Dalam Penanganan' || status === 'Ditugaskan') iconUrl = 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png';

            var icon = L.icon({
                iconUrl: iconUrl,
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
            });

            L.marker([lat, lng], {icon: icon})
                .addTo(map)
                .bindPopup("<b>Lokasi Sampah</b><br>{{ $report->address }}")
                .openPopup();
                
            // Fix map resize issue
            setTimeout(function() { map.invalidateSize(); }, 500);
        });
    </script>
    @endpush
</x-app-layout>
