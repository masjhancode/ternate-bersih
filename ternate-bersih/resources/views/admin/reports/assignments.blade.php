<x-app-layout>
    <x-slot name="header">
        <h2 class="text-sm font-bold" style="color: hsl(220 15% 13%);">Penugasan Armada Kebersihan</h2>
    </x-slot>

    <div class="mb-5 flex justify-between items-center">
        <div>
            <h3 class="text-base font-bold" style="color: hsl(220 15% 13%);">Manajemen Penugasan</h3>
            <p class="text-xs" style="color: hsl(220 8% 55%);">Tugaskan armada truk atau kendaraan operasional ke lokasi laporan yang sudah diverifikasi.</p>
        </div>
        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold" style="background: hsl(226 100% 95%); color: hsl(226 100% 50%);">
            {{ $reports->total() }} Menunggu Armada
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
            <div x-data="{ openAssign: false }" class="rounded-xl overflow-hidden bg-white flex flex-col" style="border: 1px solid hsl(220 10% 90%); box-shadow: 0 1px 4px hsl(220 15% 13% / 0.04);">
                {{-- Gambar dan Badge --}}
                <div class="h-40 overflow-hidden relative bg-gray-100">
                    <img src="{{ Storage::url($report->photo_path) }}" alt="Foto Sampah" class="w-full h-full object-cover">
                    
                    @php
                        $badgeStyle = 'background: hsl(226 100% 95%); color: hsl(226 100% 50%);'; // Diverifikasi / Ditugaskan
                    @endphp

                    <span class="absolute top-3 left-3 px-2 py-1 rounded text-[10px] font-bold shadow-sm" style="{{ $badgeStyle }} backdrop-filter: blur(4px);">
                        {{ $report->status }}
                    </span>
                    
                    @if($report->priority == 'Tinggi')
                    <span class="absolute top-3 right-3 px-2 py-1 bg-red-600 text-white rounded text-[10px] font-bold shadow-sm animate-pulse">
                        PRIORITAS TINGGI
                    </span>
                    @endif
                </div>
                
                {{-- Detail Konten --}}
                <div class="p-4 flex-1 flex flex-col">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="text-sm font-bold truncate" style="color: hsl(220 15% 13%);">{{ $report->report_number }}</h4>
                        <span class="text-[10px] whitespace-nowrap" style="color: hsl(220 8% 55%);">{{ $report->created_at->diffForHumans() }}</span>
                    </div>
                    
                    <div class="mb-3">
                        <p class="text-[10px] font-bold uppercase text-emerald-600 mb-0.5">{{ $report->category->name }} (SLA: {{ $report->category->sla_hours }} Jam)</p>
                        <p class="text-xs line-clamp-2" style="color: hsl(220 15% 30%);">{{ $report->address }}</p>
                    </div>
                    
                    <div class="flex justify-between items-center mb-4">
                        <p class="text-[10px] font-semibold" style="color: hsl(220 8% 55%);">Pelapor: {{ $report->user->name }}</p>
                        <a href="https://maps.google.com/?q={{ $report->lat }},{{ $report->lng }}" target="_blank" class="text-[10px] text-blue-600 hover:underline font-bold">Buka Maps →</a>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="pt-3 border-t mt-auto" style="border-color: hsl(220 10% 92%);">
                        @if($report->status == 'Diverifikasi')
                        <button @click="openAssign = true" type="button" class="w-full py-1.5 rounded-md text-xs font-bold transition-colors border text-white bg-blue-600 hover:bg-blue-700 border-blue-700 shadow-sm">
                            Tugaskan Armada
                        </button>
                        @else
                        <button @click="openAssign = true" type="button" class="w-full py-1.5 rounded-md text-xs font-bold transition-colors border text-blue-700 bg-blue-50 hover:bg-blue-100 border-blue-200">
                            Ubah Armada (Re-assign)
                        </button>
                        @endif
                    </div>
                </div>

                {{-- MODAL PENUGASAN ARMADA --}}
                <div x-show="openAssign" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                        <div x-show="openAssign" @click="openAssign = false" class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-50 backdrop-blur-sm"></div>
                        <div x-show="openAssign" class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl relative z-50 border border-gray-100">
                            <h3 class="text-lg font-bold leading-6 text-gray-900 mb-2">Tugaskan Armada</h3>
                            <p class="text-xs text-gray-500 mb-4">Pilih armada kebersihan yang akan diterjunkan ke lokasi laporan <span class="font-bold text-gray-800">{{ $report->report_number }}</span>.</p>
                            
                            <form action="{{ route('admin.reports.assign', $report) }}" method="POST">
                                @csrf
                                
                                <div class="mb-4">
                                    <label class="block text-xs font-bold mb-2 text-gray-700">Pilih Armada (Kendaraan)</label>
                                    <select name="fleet_id" required class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                        <option value="">-- Pilih Armada --</option>
                                        @foreach($fleets as $fleet)
                                            <option value="{{ $fleet->id }}">{{ $fleet->plate_number }} - {{ $fleet->vehicle_type }} (Kapasitas: {{ $fleet->capacity }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-5">
                                    <label class="block text-xs font-bold mb-2 text-gray-700">Catatan Penugasan (Opsional)</label>
                                    <textarea name="notes" rows="2" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="Contoh: Tolong segera diangkut, prioritas tinggi."></textarea>
                                </div>
                                
                                <div class="mt-5 sm:flex sm:flex-row-reverse gap-2">
                                    <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-blue-600 text-sm font-bold text-white shadow-sm hover:bg-blue-700 sm:w-auto">
                                        Kirim Penugasan
                                    </button>
                                    <button @click="openAssign = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 sm:mt-0 sm:w-auto">
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
                <div class="mx-auto w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center mb-3">
                    <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </div>
                <h3 class="text-sm font-bold" style="color: hsl(220 15% 30%);">Tidak Ada Laporan Tertunda</h3>
                <p class="text-xs mt-1" style="color: hsl(220 8% 55%);">Belum ada laporan yang diverifikasi untuk ditugaskan ke armada.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $reports->links() }}
    </div>
</x-app-layout>
