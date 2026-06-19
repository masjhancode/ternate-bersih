<x-app-layout>
    <x-slot name="header">
        <h2 class="text-sm font-bold" style="color: hsl(220 15% 13%);">Pengguna & Hak Akses</h2>
    </x-slot>

    <div x-data="{ openAdd: false, openEdit: false, openDelete: false, editData: {}, deleteId: null }" class="space-y-6">
        
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-base font-bold" style="color: hsl(220 15% 13%);">Manajemen Akun Pengguna</h3>
                <p class="text-xs" style="color: hsl(220 8% 55%);">Kelola data petugas, admin, dan masyarakat beserta level hak aksesnya (Role).</p>
            </div>
            <button @click="openAdd = true" type="button" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Tambah Pengguna
            </button>
        </div>

        @if(session('success'))
            <div class="p-3 rounded-lg flex items-center gap-2 text-xs font-bold" style="background: hsl(168 42% 92%); color: hsl(168 78% 21%); border: 1px solid hsl(168 42% 86%);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div class="p-3 rounded-lg flex flex-col gap-1 text-xs font-bold" style="background: hsl(0 84% 95%); color: hsl(0 84% 50%); border: 1px solid hsl(0 84% 90%);">
                @if(session('error')) <div>{{ session('error') }}</div> @endif
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
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Profil</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Kontak</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Hak Akses (Role)</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900">{{ $user->name }}</p>
                                            <p class="text-[10px] text-gray-500">NIK: {{ $user->nik ?: '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-xs text-gray-700">{{ $user->email }}</p>
                                    <p class="text-[10px] text-gray-500">{{ $user->phone_number ?: 'Tanpa No. HP' }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $badgeColor = match($user->role) {
                                            'Administrator' => 'bg-purple-100 text-purple-700',
                                            'Operator DLH' => 'bg-indigo-100 text-indigo-700',
                                            'Koordinator Lapangan' => 'bg-blue-100 text-blue-700',
                                            'Petugas Lapangan' => 'bg-emerald-100 text-emerald-700',
                                            'Driver Armada' => 'bg-amber-100 text-amber-700',
                                            default => 'bg-gray-100 text-gray-700'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $badgeColor }}">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button @click="editData = {{ json_encode($user) }}; openEdit = true" class="text-blue-600 hover:text-blue-800 text-xs font-bold mr-3">Edit</button>
                                    @if($user->id !== auth()->id())
                                        <button @click="deleteId = {{ $user->id }}; openDelete = true" class="text-red-600 hover:text-red-800 text-xs font-bold">Hapus</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-xs text-gray-500">Belum ada data pengguna.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-4">
            {{ $users->links() }}
        </div>

        {{-- MODAL TAMBAH --}}
        <div x-show="openAdd" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="openAdd" @click="openAdd = false" class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-50 backdrop-blur-sm"></div>
                <div x-show="openAdd" class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl relative z-50 border border-gray-100">
                    <h3 class="text-lg font-bold leading-6 text-gray-900 mb-4">Tambah Pengguna Baru</h3>
                    
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold mb-1.5 text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" name="name" required class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold mb-1.5 text-gray-700">Email <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" required class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold mb-1.5 text-gray-700">Password <span class="text-red-500">*</span></label>
                                    <input type="password" name="password" required class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold mb-1.5 text-gray-700">NIK (KTP)</label>
                                    <input type="text" name="nik" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold mb-1.5 text-gray-700">Nomor HP</label>
                                    <input type="text" name="phone_number" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold mb-1.5 text-gray-700">Hak Akses (Role) <span class="text-red-500">*</span></label>
                                <select name="role" required class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                    <option value="Masyarakat">Masyarakat</option>
                                    <option value="Petugas Lapangan">Petugas Lapangan</option>
                                    <option value="Driver Armada">Driver Armada</option>
                                    <option value="Koordinator Lapangan">Koordinator Lapangan</option>
                                    <option value="Operator DLH">Operator DLH</option>
                                    <option value="Administrator">Administrator</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-6 sm:flex sm:flex-row-reverse gap-2">
                            <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-blue-600 text-sm font-bold text-white shadow-sm hover:bg-blue-700 sm:w-auto">Simpan Pengguna</button>
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
                    <h3 class="text-lg font-bold leading-6 text-gray-900 mb-4">Edit Pengguna</h3>
                    
                    <form :action="`/admin/users/${editData.id}`" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold mb-1.5 text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" name="name" x-model="editData.name" required class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold mb-1.5 text-gray-700">Email <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" x-model="editData.email" required class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold mb-1.5 text-gray-700">Password Baru</label>
                                    <input type="password" name="password" placeholder="Isi jika ingin diubah" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold mb-1.5 text-gray-700">NIK (KTP)</label>
                                    <input type="text" name="nik" x-model="editData.nik" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold mb-1.5 text-gray-700">Nomor HP</label>
                                    <input type="text" name="phone_number" x-model="editData.phone_number" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold mb-1.5 text-gray-700">Hak Akses (Role) <span class="text-red-500">*</span></label>
                                <select name="role" x-model="editData.role" required class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                    <option value="Masyarakat">Masyarakat</option>
                                    <option value="Petugas Lapangan">Petugas Lapangan</option>
                                    <option value="Driver Armada">Driver Armada</option>
                                    <option value="Koordinator Lapangan">Koordinator Lapangan</option>
                                    <option value="Operator DLH">Operator DLH</option>
                                    <option value="Administrator">Administrator</option>
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
                    <h3 class="text-lg font-bold leading-6 text-red-600 mb-2">Hapus Pengguna</h3>
                    <p class="text-xs text-gray-500 mb-4">Apakah Anda yakin ingin menghapus akun pengguna ini secara permanen?</p>
                    
                    <form :action="`/admin/users/${deleteId}`" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="mt-5 sm:flex sm:flex-row-reverse gap-2">
                            <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-red-600 text-sm font-bold text-white shadow-sm hover:bg-red-700 sm:w-auto">Ya, Hapus Akun</button>
                            <button @click="openDelete = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 sm:mt-0 sm:w-auto">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
