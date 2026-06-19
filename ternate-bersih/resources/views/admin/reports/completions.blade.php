<x-app-layout>
    <x-slot name="header">
        <h2 class="text-sm font-bold" style="color: hsl(220 15% 13%);">Penyelesaian Laporan (Di Lapangan)</h2>
    </x-slot>

    <div class="mb-5 flex justify-between items-center">
        <div>
            <h3 class="text-base font-bold" style="color: hsl(220 15% 13%);">Pembaruan Status Selesai</h3>
            <p class="text-xs" style="color: hsl(220 8% 55%);">Unggah foto bukti bahwa tumpukan sampah telah berhasil dibersihkan oleh armada.</p>
        </div>
        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold" style="background: hsl(226 100% 95%); color: hsl(226 100% 50%);">
            {{ $reports->total() }} Sedang Dikerjakan
        </span>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 rounded-lg flex items-center gap-2 text-xs font-bold" style="background: hsl(168 42% 92%); color: hsl(168 78% 21%); border: 1px solid hsl(168 42% 86%);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif
    
    @if($errors->any())
        <div class="mb-4 p-3 rounded-lg flex flex-col gap-1 text-xs font-bold" style="background: hsl(0 84% 95%); color: hsl(0 84% 50%); border: 1px solid hsl(0 84% 90%);">
            @foreach($errors->all() as $error)
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $error }}
                </div>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($reports as $report)
            <div x-data="{ 
                openComplete: false, 
                imagePreview: null,
                previewImage(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => { this.imagePreview = e.target.result; };
                        reader.readAsDataURL(file);
                    } else {
                        this.imagePreview = null;
                    }
                }
            }" class="rounded-xl overflow-hidden bg-white flex flex-col" style="border: 1px solid hsl(220 10% 90%); box-shadow: 0 1px 4px hsl(220 15% 13% / 0.04);">
                
                {{-- Gambar dan Badge --}}
                <div class="h-40 overflow-hidden relative bg-gray-100">
                    <img src="{{ Storage::url($report->photo_path) }}" alt="Foto Sampah" class="w-full h-full object-cover grayscale opacity-80" title="Foto Sebelum">
                    
                    <span class="absolute top-3 left-3 px-2 py-1 rounded text-[10px] font-bold shadow-sm" style="background: hsl(226 100% 95%); color: hsl(226 100% 50%); backdrop-filter: blur(4px);">
                        {{ $report->status }}
                    </span>
                    
                    <span class="absolute bottom-3 right-3 px-2 py-1 bg-black/60 text-white rounded text-[10px] font-bold shadow-sm backdrop-blur-sm">
                        Kondisi: SEBELUM (Kotor)
                    </span>
                </div>
                
                {{-- Detail Konten --}}
                <div class="p-4 flex-1 flex flex-col">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="text-sm font-bold truncate" style="color: hsl(220 15% 13%);">{{ $report->report_number }}</h4>
                        <span class="text-[10px] whitespace-nowrap text-blue-600 font-semibold flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Sedang Jalan
                        </span>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-[10px] font-bold uppercase text-emerald-600 mb-0.5">{{ $report->category->name }}</p>
                        <p class="text-xs line-clamp-2" style="color: hsl(220 15% 30%);">{{ $report->address }}</p>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="pt-3 border-t mt-auto" style="border-color: hsl(220 10% 92%);">
                        <button @click="openComplete = true" type="button" class="w-full flex items-center justify-center gap-2 py-2 rounded-md text-xs font-bold transition-colors border text-white shadow-sm" style="background: hsl(168 78% 21%); border-color: hsl(168 78% 15%); hover:background: hsl(168 78% 15%);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Upload Foto Selesai
                        </button>
                    </div>
                </div>

                {{-- MODAL PENYELESAIAN (UPLOAD FOTO SESUDAH) --}}
                <div x-show="openComplete" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                        <div x-show="openComplete" @click="openComplete = false" class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-50 backdrop-blur-sm"></div>
                        <div x-show="openComplete" class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl relative z-50 border border-emerald-100">
                            
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold leading-6 text-gray-900">Konfirmasi Penyelesaian</h3>
                                    <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider">{{ $report->report_number }}</p>
                                </div>
                            </div>
                            
                            <form action="{{ route('admin.reports.complete', $report) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="mb-4">
                                    <label class="block text-xs font-bold mb-2 text-gray-700">Foto Bukti Selesai (Bersih) <span class="text-red-500">*</span></label>
                                    
                                    {{-- Custom File Upload Component --}}
                                    <div class="relative group cursor-pointer">
                                        <div class="w-full h-40 rounded-xl border-2 border-dashed flex flex-col items-center justify-center overflow-hidden transition-colors"
                                             :class="imagePreview ? 'border-emerald-500 bg-black' : 'border-emerald-200 bg-emerald-50 group-hover:bg-emerald-100'">
                                            
                                            <template x-if="!imagePreview">
                                                <div class="text-center p-4">
                                                    <svg class="mx-auto h-8 w-8 text-emerald-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    <p class="text-xs font-semibold text-emerald-700">Klik untuk Pilih Foto (Max 5MB)</p>
                                                </div>
                                            </template>
                                            
                                            <template x-if="imagePreview">
                                                <img :src="imagePreview" class="w-full h-full object-contain opacity-90 group-hover:opacity-100 transition-opacity">
                                            </template>
                                        </div>
                                        <input type="file" name="photo_after" accept="image/*" required @change="previewImage" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    </div>
                                </div>

                                <div class="mb-5">
                                    <label class="block text-xs font-bold mb-2 text-gray-700">Catatan Petugas</label>
                                    <textarea name="notes" required rows="2" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200" placeholder="Contoh: Tumpukan sampah telah dibersihkan sepenuhnya."></textarea>
                                </div>
                                
                                <div class="mt-5 sm:flex sm:flex-row-reverse gap-2">
                                    <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent px-4 py-2 text-sm font-bold text-white shadow-sm sm:w-auto transition-colors" style="background: hsl(168 78% 21%); hover:background: hsl(168 78% 15%);">
                                        Simpan & Selesai
                                    </button>
                                    <button @click="openComplete = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 sm:mt-0 sm:w-auto">
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
                <div class="mx-auto w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-sm font-bold" style="color: hsl(220 15% 30%);">Belum Ada Tugas Armada</h3>
                <p class="text-xs mt-1" style="color: hsl(220 8% 55%);">Semua laporan armada telah selesai dikerjakan.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $reports->links() }}
    </div>
</x-app-layout>
