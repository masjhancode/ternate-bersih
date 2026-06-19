<x-app-layout>
    <x-slot name="header">
        <h2 class="text-sm font-bold" style="color: hsl(220 15% 13%);">Manajemen Armada</h2>
    </x-slot>

    <div x-data="{ openAdd: false, openEdit: false, openDelete: false, editData: {}, deleteId: null }" class="space-y-6">
        
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-base font-bold" style="color: hsl(220 15% 13%);">Data Kendaraan Pengangkut</h3>
                <p class="text-xs" style="color: hsl(220 8% 55%);">Kelola daftar armada yang akan digunakan untuk mengangkut sampah.</p>
            </div>
            <button @click="openAdd = true" type="button" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Armada
            </button>
        </div>

        @if(session('success'))
            <div class="p-3 rounded-lg flex items-center gap-2 text-xs font-bold" style="background: hsl(168 42% 92%); color: hsl(168 78% 21%); border: 1px solid hsl(168 42% 86%);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-3 rounded-lg flex items-center gap-2 text-xs font-bold" style="background: hsl(0 84% 95%); color: hsl(0 84% 50%); border: 1px solid hsl(0 84% 90%);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif
        
        @if($errors->any())
            <div class="p-3 rounded-lg flex flex-col gap-1 text-xs font-bold" style="background: hsl(0 84% 95%); color: hsl(0 84% 50%); border: 1px solid hsl(0 84% 90%);">
                @foreach($errors->all() as $error)
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $error }}
                    </div>
                @endforeach
            </div>
        @endif

        <div class="bg-white rounded-xl overflow-hidden" style="border: 1px solid hsl(220 10% 90%); box-shadow: 0 1px 4px hsl(220 15% 13% / 0.04);">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr style="background: hsl(220 10% 97%); border-bottom: 1px solid hsl(220 10% 90%);">
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Pelat Nomor</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Jenis Kendaraan</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Driver / Sopir</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Kapasitas</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($fleets as $fleet)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 font-bold" style="color: hsl(220 15% 13%);">{{ $fleet->plate_number }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $fleet->vehicle_type }}</td>
                                <td class="px-4 py-3">
                                    @if($fleet->user)
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-[10px]">
                                                {{ substr($fleet->user->name, 0, 1) }}
                                            </div>
                                            <span class="text-sm font-medium text-gray-700">{{ $fleet->user->name }}</span>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700">
                                            Belum Ditugaskan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-700">
                                        {{ $fleet->capacity ?: '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button @click="editData = {{ json_encode($fleet) }}; openEdit = true" class="text-blue-600 hover:text-blue-800 text-xs font-bold mr-3">Edit</button>
                                    <button @click="deleteId = {{ $fleet->id }}; openDelete = true" class="text-red-600 hover:text-red-800 text-xs font-bold">Hapus</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-xs text-gray-500">Belum ada data armada. Silakan tambah baru.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-4">
            {{ $fleets->links() }}
        </div>

        {{-- MODAL TAMBAH --}}
        <div x-show="openAdd" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="openAdd" @click="openAdd = false" class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-50 backdrop-blur-sm"></div>
                <div x-show="openAdd" class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl relative z-50 border border-gray-100">
                    <h3 class="text-lg font-bold leading-6 text-gray-900 mb-4">Tambah Armada</h3>
                    
                    <form action="{{ route('admin.fleets.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold mb-1.5 text-gray-700">Pelat Nomor <span class="text-red-500">*</span></label>
                                <input type="text" name="plate_number" required class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200" placeholder="Contoh: DG 8001 A">
                            </div>
                            <div>
                                <label class="block text-xs font-bold mb-1.5 text-gray-700">Jenis Kendaraan <span class="text-red-500">*</span></label>
                                <input type="text" name="vehicle_type" required class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200" placeholder="Contoh: Truk Sampah Besar">
                            </div>
                            <div>
                                <label class="block text-xs font-bold mb-1.5 text-gray-700">Kapasitas (Opsional)</label>
                                <input type="text" name="capacity" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200" placeholder="Contoh: 6 Ton">
                            </div>
                            <div>
                                <label class="block text-xs font-bold mb-1.5 text-gray-700">Driver / Sopir Ditugaskan</label>
                                <select name="user_id" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                                    <option value="">-- Pilih Driver (Opsional) --</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-[10px] text-gray-500">Anda dapat menugaskan driver nanti.</p>
                            </div>
                        </div>
                        <div class="mt-6 sm:flex sm:flex-row-reverse gap-2">
                            <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-emerald-600 text-sm font-bold text-white shadow-sm hover:bg-emerald-700 sm:w-auto">Simpan</button>
                            <button @click="openAdd = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 sm:mt-0 sm:w-auto">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL EDIT --}}
        <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="openEdit" @click="openEdit = false" class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-50 backdrop-blur-sm"></div>
                <div x-show="openEdit" class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl relative z-50 border border-gray-100">
                    <h3 class="text-lg font-bold leading-6 text-gray-900 mb-4">Edit Armada</h3>
                    
                    <form :action="`/admin/fleets/${editData.id}`" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold mb-1.5 text-gray-700">Pelat Nomor <span class="text-red-500">*</span></label>
                                <input type="text" name="plate_number" x-model="editData.plate_number" required class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                            </div>
                            <div>
                                <label class="block text-xs font-bold mb-1.5 text-gray-700">Jenis Kendaraan <span class="text-red-500">*</span></label>
                                <input type="text" name="vehicle_type" x-model="editData.vehicle_type" required class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                            </div>
                            <div>
                                <label class="block text-xs font-bold mb-1.5 text-gray-700">Kapasitas (Opsional)</label>
                                <input type="text" name="capacity" x-model="editData.capacity" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                            </div>
                            <div>
                                <label class="block text-xs font-bold mb-1.5 text-gray-700">Driver / Sopir Ditugaskan</label>
                                <select name="user_id" x-model="editData.user_id" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                                    <option value="">-- Pilih Driver (Opsional) --</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-6 sm:flex sm:flex-row-reverse gap-2">
                            <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-blue-600 text-sm font-bold text-white shadow-sm hover:bg-blue-700 sm:w-auto">Simpan Perubahan</button>
                            <button @click="openEdit = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 sm:mt-0 sm:w-auto">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL HAPUS --}}
        <div x-show="openDelete" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="openDelete" @click="openDelete = false" class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-50 backdrop-blur-sm"></div>
                <div x-show="openDelete" class="inline-block w-full max-w-sm p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl relative z-50 border border-red-100">
                    <h3 class="text-lg font-bold leading-6 text-red-600 mb-2">Hapus Armada</h3>
                    <p class="text-xs text-gray-500 mb-4">Apakah Anda yakin ingin menghapus armada ini? Data armada yang telah dihapus tidak dapat dikembalikan.</p>
                    
                    <form :action="`/admin/fleets/${deleteId}`" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="mt-5 sm:flex sm:flex-row-reverse gap-2">
                            <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-red-600 text-sm font-bold text-white shadow-sm hover:bg-red-700 sm:w-auto">Ya, Hapus</button>
                            <button @click="openDelete = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 sm:mt-0 sm:w-auto">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
