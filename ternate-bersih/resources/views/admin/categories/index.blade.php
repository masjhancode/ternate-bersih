<x-app-layout>
    <x-slot name="header">
        <h2 class="text-sm font-bold" style="color: hsl(220 15% 13%);">Manajemen Kategori Sampah</h2>
    </x-slot>

    <div class="mb-5 flex justify-between items-center">
        <div>
            <h3 class="text-base font-bold" style="color: hsl(220 15% 13%);">Daftar Kategori</h3>
            <p class="text-xs" style="color: hsl(220 8% 55%);">Kelola kategori laporan dan target waktu penanganan (SLA).</p>
        </div>
        <a href="{{ route('categories.create') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold text-white shadow-sm transition-transform hover:scale-105" style="background: hsl(168 78% 21%);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Kategori
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 rounded-lg flex items-center gap-2 text-xs font-bold" style="background: hsl(168 42% 92%); color: hsl(168 78% 21%); border: 1px solid hsl(168 42% 86%);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-xl overflow-hidden bg-white" style="border: 1px solid hsl(220 10% 90%); box-shadow: 0 1px 4px hsl(220 15% 13% / 0.04);">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead style="background: hsl(220 10% 97%); border-bottom: 1px solid hsl(220 10% 90%);">
                    <tr>
                        <th class="px-4 py-3 font-bold text-xs uppercase tracking-wider" style="color: hsl(220 15% 30%);">No</th>
                        <th class="px-4 py-3 font-bold text-xs uppercase tracking-wider" style="color: hsl(220 15% 30%);">Nama Kategori</th>
                        <th class="px-4 py-3 font-bold text-xs uppercase tracking-wider" style="color: hsl(220 15% 30%);">Target SLA (Jam)</th>
                        <th class="px-4 py-3 font-bold text-xs uppercase tracking-wider text-right" style="color: hsl(220 15% 30%);">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="divide-color: hsl(220 10% 92%);">
                    @forelse($categories as $category)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-xs" style="color: hsl(220 8% 45%);">{{ $loop->iteration + $categories->firstItem() - 1 }}</td>
                        <td class="px-4 py-3 font-semibold" style="color: hsl(220 15% 13%);">{{ $category->name }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold" style="background: hsl(45 93% 92%); color: hsl(45 93% 35%);">
                                {{ $category->sla_hours }} Jam
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('categories.edit', $category) }}" class="p-1.5 rounded-md hover:bg-slate-200 transition-colors" style="color: hsl(220 15% 30%);" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-md hover:bg-red-50 transition-colors" style="color: hsl(0 84% 50%);" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-xs" style="color: hsl(220 8% 55%);">Belum ada data kategori sampah.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t bg-gray-50" style="border-color: hsl(220 10% 90%);">
            {{ $categories->links() }}
        </div>
    </div>
</x-app-layout>
