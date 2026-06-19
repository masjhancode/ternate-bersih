<x-app-layout>
    <x-slot name="header">
        <h2 class="text-sm font-bold" style="color: hsl(220 15% 13%);">Manajemen Wilayah</h2>
    </x-slot>

    <div class="mb-5 flex justify-between items-center">
        <div>
            <h3 class="text-base font-bold" style="color: hsl(220 15% 13%);">Data Kecamatan & Kelurahan</h3>
            <p class="text-xs" style="color: hsl(220 8% 55%);">Kelola struktur wilayah Kota Ternate untuk pemetaan lokasi sampah.</p>
        </div>
        <div x-data="{ openAddDistrict: false }">
            <button @click="openAddDistrict = true" type="button" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Kecamatan
            </button>
            
            {{-- MODAL TAMBAH KECAMATAN --}}
            <div x-show="openAddDistrict" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="flex items-center justify-center min-h-screen px-4 text-center">
                    <div x-show="openAddDistrict" @click="openAddDistrict = false" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm"></div>
                    <div x-show="openAddDistrict" class="inline-block w-full max-w-md p-6 overflow-hidden text-left bg-white shadow-xl rounded-2xl relative z-50 border border-indigo-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Tambah Kecamatan Baru</h3>
                        <form action="{{ route('admin.regions.districts.store') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-xs font-bold mb-1.5 text-gray-700">Nama Kecamatan</label>
                                <input type="text" name="name" required class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200" placeholder="Contoh: Ternate Tengah">
                            </div>
                            <div class="flex justify-end gap-2 mt-5">
                                <button type="button" @click="openAddDistrict = false" class="px-4 py-2 text-sm font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg">Batal</button>
                                <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 rounded-lg flex items-center gap-2 text-xs font-bold" style="background: hsl(168 42% 92%); color: hsl(168 78% 21%); border: 1px solid hsl(168 42% 86%);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error') || $errors->any())
        <div class="mb-4 p-3 rounded-lg flex flex-col gap-1 text-xs font-bold" style="background: hsl(0 84% 95%); color: hsl(0 84% 50%); border: 1px solid hsl(0 84% 90%);">
            @if(session('error')) 
                <div>{{ session('error') }}</div>
            @endif
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($districts as $district)
            <div x-data="{ openAddVillage: false, openEditDistrict: false, editDistrictName: '{{ $district->name }}' }" class="rounded-xl overflow-hidden bg-white flex flex-col" style="border: 1px solid hsl(220 10% 90%); box-shadow: 0 1px 4px hsl(220 15% 13% / 0.04);">
                
                {{-- Header Kecamatan --}}
                <div class="p-4 border-b flex justify-between items-center" style="background: hsl(210 100% 98%); border-color: hsl(220 10% 92%);">
                    <h4 class="text-sm font-bold text-indigo-900 flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Kec. {{ $district->name }}
                    </h4>
                    <div class="flex items-center gap-2">
                        <button @click="openEditDistrict = true" class="text-gray-400 hover:text-indigo-600 transition-colors" title="Edit Kecamatan">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                        <form action="{{ route('admin.regions.districts.destroy', $district) }}" method="POST" onsubmit="return confirm('Hapus Kecamatan ini?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors" title="Hapus Kecamatan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- List Kelurahan --}}
                <div class="p-0 flex-1">
                    @if($district->villages->count() > 0)
                        <ul class="divide-y divide-gray-100">
                            @foreach($district->villages as $village)
                                <li x-data="{ openEditVillage: false, editVillageName: '{{ $village->name }}' }" class="p-3 text-xs flex justify-between items-center hover:bg-gray-50 transition-colors">
                                    <span class="font-medium text-gray-700">Kel. {{ $village->name }}</span>
                                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100" style="opacity: 1;">
                                        <button @click="openEditVillage = true" class="text-blue-500 hover:underline">Edit</button>
                                        <form action="{{ route('admin.regions.villages.destroy', $village) }}" method="POST" onsubmit="return confirm('Hapus Kelurahan ini?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:underline">Hapus</button>
                                        </form>
                                    </div>

                                    {{-- MODAL EDIT KELURAHAN --}}
                                    <div x-show="openEditVillage" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                        <div class="flex items-center justify-center min-h-screen px-4 text-center">
                                            <div x-show="openEditVillage" @click="openEditVillage = false" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm"></div>
                                            <div x-show="openEditVillage" class="inline-block w-full max-w-sm p-6 overflow-hidden text-left bg-white shadow-xl rounded-2xl relative z-50">
                                                <h3 class="text-base font-bold text-gray-900 mb-4">Edit Kelurahan</h3>
                                                <form action="{{ route('admin.regions.villages.update', $village) }}" method="POST">
                                                    @csrf @method('PUT')
                                                    <input type="hidden" name="district_id" value="{{ $district->id }}">
                                                    <div class="mb-4">
                                                        <label class="block text-xs font-bold mb-1.5">Nama Kelurahan</label>
                                                        <input type="text" name="name" x-model="editVillageName" required class="w-full text-sm rounded-lg border-gray-300">
                                                    </div>
                                                    <div class="flex justify-end gap-2">
                                                        <button type="button" @click="openEditVillage = false" class="px-3 py-1.5 text-xs font-bold bg-gray-100 rounded-lg">Batal</button>
                                                        <button type="submit" class="px-3 py-1.5 text-xs font-bold text-white bg-blue-600 rounded-lg">Simpan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="p-6 text-center text-xs text-gray-400">Belum ada Kelurahan.</div>
                    @endif
                </div>

                {{-- Footer Tambah Kelurahan --}}
                <div class="p-3 border-t bg-gray-50 mt-auto" style="border-color: hsl(220 10% 92%);">
                    <button @click="openAddVillage = true" class="w-full py-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 rounded border border-indigo-200 transition-colors">
                        + Tambah Kelurahan
                    </button>
                </div>

                {{-- MODAL TAMBAH KELURAHAN --}}
                <div x-show="openAddVillage" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                    <div class="flex items-center justify-center min-h-screen px-4 text-center">
                        <div x-show="openAddVillage" @click="openAddVillage = false" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm"></div>
                        <div x-show="openAddVillage" class="inline-block w-full max-w-sm p-6 overflow-hidden text-left bg-white shadow-xl rounded-2xl relative z-50">
                            <h3 class="text-base font-bold text-gray-900 mb-4">Tambah Kelurahan (Kec. {{ $district->name }})</h3>
                            <form action="{{ route('admin.regions.villages.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="district_id" value="{{ $district->id }}">
                                <div class="mb-4">
                                    <label class="block text-xs font-bold mb-1.5">Nama Kelurahan</label>
                                    <input type="text" name="name" required class="w-full text-sm rounded-lg border-gray-300">
                                </div>
                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="openAddVillage = false" class="px-3 py-1.5 text-xs font-bold bg-gray-100 rounded-lg">Batal</button>
                                    <button type="submit" class="px-3 py-1.5 text-xs font-bold text-white bg-indigo-600 rounded-lg">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- MODAL EDIT KECAMATAN --}}
                <div x-show="openEditDistrict" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                    <div class="flex items-center justify-center min-h-screen px-4 text-center">
                        <div x-show="openEditDistrict" @click="openEditDistrict = false" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm"></div>
                        <div x-show="openEditDistrict" class="inline-block w-full max-w-sm p-6 overflow-hidden text-left bg-white shadow-xl rounded-2xl relative z-50">
                            <h3 class="text-base font-bold text-gray-900 mb-4">Edit Kecamatan</h3>
                            <form action="{{ route('admin.regions.districts.update', $district) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="mb-4">
                                    <label class="block text-xs font-bold mb-1.5">Nama Kecamatan</label>
                                    <input type="text" name="name" x-model="editDistrictName" required class="w-full text-sm rounded-lg border-gray-300">
                                </div>
                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="openEditDistrict = false" class="px-3 py-1.5 text-xs font-bold bg-gray-100 rounded-lg">Batal</button>
                                    <button type="submit" class="px-3 py-1.5 text-xs font-bold text-white bg-indigo-600 rounded-lg">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full py-12 text-center rounded-xl bg-white border border-dashed border-gray-300">
                <div class="mx-auto w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-700">Data Wilayah Kosong</h3>
                <p class="text-xs text-gray-500 mt-1">Belum ada data Kecamatan yang ditambahkan.</p>
            </div>
        @endforelse
    </div>

</x-app-layout>
