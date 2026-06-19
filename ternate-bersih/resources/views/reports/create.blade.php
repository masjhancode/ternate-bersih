<x-app-layout>
    <x-slot name="header">
        <h2 class="text-sm font-bold" style="color: hsl(220 15% 13%);">Kirim Laporan Sampah</h2>
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold hover:underline" style="color: hsl(168 78% 21%);">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Riwayat Laporan
        </a>
    </div>

    <div class="rounded-xl overflow-hidden bg-white" style="border: 1px solid hsl(220 10% 90%); box-shadow: 0 1px 4px hsl(220 15% 13% / 0.04);">
        <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data" class="p-5">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Kiri: Detail Laporan & Foto --}}
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider mb-4 border-b pb-2" style="color: hsl(220 8% 55%); border-color: hsl(220 10% 92%);">1. Detail Laporan</h3>
                    
                    <div class="mb-4">
                        <label for="category_id" class="block text-xs font-bold mb-1.5" style="color: hsl(220 15% 30%);">Kategori Sampah <span class="text-red-500">*</span></label>
                        <select name="category_id" id="category_id" required class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200" style="color: hsl(220 15% 13%);">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }} (SLA: {{ $category->sla_hours }} Jam)</option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="text-[10px] text-red-600 mt-1 font-semibold">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label for="photo" class="block text-xs font-bold mb-1.5" style="color: hsl(220 15% 30%);">Foto Bukti Sampah <span class="text-red-500">*</span></label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-lg" style="border-color: hsl(220 10% 85%); background: hsl(210 20% 99%);">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <label for="photo" class="relative cursor-pointer rounded-md font-medium text-emerald-600 hover:text-emerald-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-emerald-500">
                                        <span>Unggah file foto</span>
                                        <input id="photo" name="photo" type="file" class="sr-only" accept="image/*" required onchange="previewImage(event)">
                                    </label>
                                </div>
                                <p class="text-[10px] text-gray-500">PNG, JPG, JPEG hingga 5MB</p>
                            </div>
                        </div>
                        <img id="image_preview" class="mt-3 rounded-lg max-h-48 object-cover hidden shadow-sm border border-gray-200" alt="Preview" />
                        @error('photo')<p class="text-[10px] text-red-600 mt-1 font-semibold">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-xs font-bold mb-1.5" style="color: hsl(220 15% 30%);">Deskripsi Tambahan</label>
                        <textarea name="description" id="description" rows="3" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200" placeholder="Kondisi jalan terhalang, bau menyengat, dll.">{{ old('description') }}</textarea>
                    </div>
                </div>

                {{-- Kanan: Lokasi & Map --}}
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider mb-4 border-b pb-2" style="color: hsl(220 8% 55%); border-color: hsl(220 10% 92%);">2. Lokasi Geotagging</h3>
                    
                    <div class="mb-4">
                        <label class="block text-xs font-bold mb-1.5" style="color: hsl(220 15% 30%);">Tandai Lokasi di Peta <span class="text-red-500">*</span></label>
                        <p class="text-[10px] text-gray-500 mb-2">Geser peta atau klik pada lokasi tumpukan sampah untuk mendapatkan koordinat yang akurat.</p>
                        <div id="form-map" class="w-full rounded-lg mb-2" style="height: 250px; z-index: 10;"></div>
                        
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <input type="text" name="lat" id="lat" value="{{ old('lat', 0.7893) }}" readonly class="w-full text-xs bg-gray-50 rounded border-gray-300" placeholder="Latitude">
                            </div>
                            <div class="flex-1">
                                <input type="text" name="lng" id="lng" value="{{ old('lng', 127.3540) }}" readonly class="w-full text-xs bg-gray-50 rounded border-gray-300" placeholder="Longitude">
                            </div>
                            <button type="button" id="btn-my-location" class="px-2 py-1 bg-blue-50 text-blue-600 border border-blue-200 rounded text-xs font-bold hover:bg-blue-100 transition-colors" title="Gunakan Lokasi Saat Ini">
                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="address" class="block text-xs font-bold mb-1.5" style="color: hsl(220 15% 30%);">Alamat Lengkap / Patokan <span class="text-red-500">*</span></label>
                        <textarea name="address" id="address" rows="2" required class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200" placeholder="Contoh: Depan pasar induk, dekat tiang listrik">{{ old('address') }}</textarea>
                        @error('address')<p class="text-[10px] text-red-600 mt-1 font-semibold">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="pt-4 mt-2 border-t flex justify-end" style="border-color: hsl(220 10% 90%);">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-bold text-white shadow-sm transition-transform hover:scale-105" style="background: hsl(168 78% 21%);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Kirim Laporan
                </button>
            </div>
        </form>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    @endpush

    @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function(){
                var output = document.getElementById('image_preview');
                output.src = reader.result;
                output.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        document.addEventListener('DOMContentLoaded', function() {
            var initialLat = document.getElementById('lat').value;
            var initialLng = document.getElementById('lng').value;
            
            var map = L.map('form-map').setView([initialLat, initialLng], 14);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 20
            }).addTo(map);

            var marker = L.marker([initialLat, initialLng], {draggable: true}).addTo(map);

            // Update form when marker is dragged
            marker.on('dragend', function(e) {
                var position = marker.getLatLng();
                document.getElementById('lat').value = position.lat.toFixed(6);
                document.getElementById('lng').value = position.lng.toFixed(6);
            });

            // Update marker when map is clicked
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                document.getElementById('lat').value = e.latlng.lat.toFixed(6);
                document.getElementById('lng').value = e.latlng.lng.toFixed(6);
            });

            // Geolocation Feature
            document.getElementById('btn-my-location').addEventListener('click', function() {
                if (navigator.geolocation) {
                    this.innerHTML = '<svg class="w-4 h-4 inline animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>';
                    navigator.geolocation.getCurrentPosition(function(position) {
                        var lat = position.coords.latitude;
                        var lng = position.coords.longitude;
                        
                        map.setView([lat, lng], 16);
                        marker.setLatLng([lat, lng]);
                        document.getElementById('lat').value = lat.toFixed(6);
                        document.getElementById('lng').value = lng.toFixed(6);
                        
                        document.getElementById('btn-my-location').innerHTML = '<svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
                    }, function(error) {
                        alert("Tidak dapat mengambil lokasi. Pastikan izin lokasi (GPS) diaktifkan.");
                        document.getElementById('btn-my-location').innerHTML = '<svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
                    }, { enableHighAccuracy: true });
                } else {
                    alert("Browser Anda tidak mendukung fitur Geolocation.");
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
