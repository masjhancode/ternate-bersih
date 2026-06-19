<x-app-layout>
    <x-slot name="header">
        <h2 class="text-sm font-bold" style="color: hsl(220 15% 13%);">Verifikasi Laporan Masuk</h2>
    </x-slot>

    <div class="mb-5 flex justify-between items-center">
        <div>
            <h3 class="text-base font-bold" style="color: hsl(220 15% 13%);">Antrean Verifikasi</h3>
            <p class="text-xs" style="color: hsl(220 8% 55%);">Tinjau laporan warga dan tentukan prioritas penanganan sebelum ditugaskan ke armada.</p>
        </div>
        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold" style="background: hsl(45 93% 92%); color: hsl(45 93% 35%);">
            {{ $reports->total() }} Menunggu
        </span>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 rounded-lg flex items-center gap-2 text-xs font-bold" style="background: hsl(168 42% 92%); color: hsl(168 78% 21%); border: 1px solid hsl(168 42% 86%);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="mb-4 p-3 rounded-lg flex items-center gap-2 text-xs font-bold" style="background: hsl(0 84% 95%); color: hsl(0 84% 50%); border: 1px solid hsl(0 84% 90%);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($reports as $report)
            <div x-data="{ openVerify: false, openReject: false }" class="rounded-xl overflow-hidden bg-white flex flex-col" style="border: 1px solid hsl(220 10% 90%); box-shadow: 0 1px 4px hsl(220 15% 13% / 0.04);">
                {{-- Gambar dan Badge --}}
                <div class="h-40 overflow-hidden relative bg-gray-100">
                    <img src="{{ Storage::url($report->photo_path) }}" alt="Foto Sampah" class="w-full h-full object-cover">
                    <span class="absolute top-3 left-3 px-2 py-1 rounded text-[10px] font-bold shadow-sm" style="background: hsl(45 93% 92%); color: hsl(45 93% 35%); backdrop-filter: blur(4px);">
                        {{ $report->status }}
                    </span>
                    <a href="https://maps.google.com/?q={{ $report->lat }},{{ $report->lng }}" target="_blank" class="absolute bottom-3 right-3 px-2 py-1 bg-black/60 hover:bg-black/80 text-white rounded text-[10px] font-bold backdrop-blur-sm shadow-sm transition-colors flex items-center gap-1" title="Buka di Maps">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Lihat Lokasi
                    </a>
                </div>
                
                {{-- Detail Konten --}}
                <div class="p-4 flex-1 flex flex-col">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="text-sm font-bold truncate" style="color: hsl(220 15% 13%);">{{ $report->report_number }}</h4>
                        <span class="text-[10px] whitespace-nowrap" style="color: hsl(220 8% 55%);">{{ $report->created_at->diffForHumans() }}</span>
                    </div>
                    
                    <div class="mb-3">
                        <p class="text-[10px] font-bold uppercase text-emerald-600 mb-0.5">{{ $report->category->name }}</p>
                        <p class="text-xs line-clamp-2" style="color: hsl(220 15% 30%);">{{ $report->address }}</p>
                    </div>
                    
                    <p class="text-[10px] mb-4 font-semibold" style="color: hsl(220 8% 55%);">Pelapor: {{ $report->user->name }}</p>

                    {{-- Tombol Aksi --}}
                    <div class="pt-3 border-t mt-auto grid grid-cols-2 gap-2" style="border-color: hsl(220 10% 92%);">
                        <button @click="openVerify = true" type="button" class="w-full py-1.5 rounded-md text-xs font-bold transition-colors border text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border-emerald-200">
                            Terima
                        </button>
                        <button @click="openReject = true" type="button" class="w-full py-1.5 rounded-md text-xs font-bold transition-colors border text-red-700 bg-red-50 hover:bg-red-100 border-red-200">
                            Tolak
                        </button>
                    </div>
                </div>

                {{-- MODAL VERIFIKASI (TERIMA) --}}
                <div x-show="openVerify" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                        <div x-show="openVerify" @click="openVerify = false" class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-50 backdrop-blur-sm"></div>
                        <div x-show="openVerify" class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl relative z-50 border border-gray-100">
                            <h3 class="text-lg font-bold leading-6 text-gray-900 mb-2">Verifikasi Laporan</h3>
                            <p class="text-xs text-gray-500 mb-4">Pilih prioritas penanganan untuk laporan <span class="font-bold text-gray-800">{{ $report->report_number }}</span>. Laporan ini akan masuk ke antrean penugasan armada.</p>
                            
                            <form action="{{ route('admin.reports.verify', $report) }}" method="POST">
                                @csrf
                                <input type="hidden" name="action" value="terima">
                                
                                <div class="mb-5">
                                    <label class="block text-xs font-bold mb-2 text-gray-700">Tingkat Prioritas</label>
                                    <select name="priority" required class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                                        <option value="Rendah" {{ $report->priority == 'Rendah' ? 'selected' : '' }}>Rendah (Sesuai SLA)</option>
                                        <option value="Sedang" {{ $report->priority == 'Sedang' ? 'selected' : '' }}>Sedang (Dipercepat)</option>
                                        <option value="Tinggi" {{ $report->priority == 'Tinggi' ? 'selected' : '' }}>Tinggi (Mendesak/Segera)</option>
                                    </select>
                                </div>
                                
                                <div class="mt-5 sm:flex sm:flex-row-reverse gap-2">
                                    <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-emerald-600 text-sm font-bold text-white shadow-sm hover:bg-emerald-700 sm:w-auto">
                                        Setujui & Verifikasi
                                    </button>
                                    <button @click="openVerify = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 sm:mt-0 sm:w-auto">
                                        Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- MODAL PENOLAKAN (TOLAK) --}}
                <div x-show="openReject" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                        <div x-show="openReject" @click="openReject = false" class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-50 backdrop-blur-sm"></div>
                        <div x-show="openReject" class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl relative z-50 border border-red-100">
                            <h3 class="text-lg font-bold leading-6 text-red-600 mb-2">Tolak Laporan</h3>
                            <p class="text-xs text-gray-500 mb-4">Apakah Anda yakin ingin menolak laporan <span class="font-bold text-gray-800">{{ $report->report_number }}</span>? Laporan tidak akan dilanjutkan ke armada.</p>
                            
                            <form action="{{ route('admin.reports.verify', $report) }}" method="POST">
                                @csrf
                                <input type="hidden" name="action" value="tolak">
                                
                                <div class="mb-5">
                                    <label class="block text-xs font-bold mb-2 text-gray-700">Alasan Penolakan <span class="text-red-500">*</span></label>
                                    <textarea name="description" required rows="3" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200" placeholder="Contoh: Bukan wilayah kewenangan Pemkot, foto tidak jelas, atau laporan palsu/spam."></textarea>
                                </div>
                                
                                <div class="mt-5 sm:flex sm:flex-row-reverse gap-2">
                                    <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-red-600 text-sm font-bold text-white shadow-sm hover:bg-red-700 sm:w-auto">
                                        Ya, Tolak Laporan
                                    </button>
                                    <button @click="openReject = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 sm:mt-0 sm:w-auto">
                                        Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full py-12 text-center rounded-xl" style="background: white; border: 1px dashed hsl(220 10% 85%);">
                <div class="mx-auto w-12 h-12 bg-emerald-50 rounded-full flex items-center justify-center mb-3">
                    <svg class="h-6 w-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="text-sm font-bold" style="color: hsl(220 15% 30%);">Antrean Verifikasi Bersih</h3>
                <p class="text-xs mt-1" style="color: hsl(220 8% 55%);">Semua laporan telah selesai ditinjau. Kerja bagus!</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $reports->links() }}
    </div>
</x-app-layout>
