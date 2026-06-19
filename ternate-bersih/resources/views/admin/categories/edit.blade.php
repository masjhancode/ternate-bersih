<x-app-layout>
    <x-slot name="header">
        <h2 class="text-sm font-bold" style="color: hsl(220 15% 13%);">Edit Kategori Sampah</h2>
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('categories.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold hover:underline" style="color: hsl(168 78% 21%);">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar
        </a>
    </div>

    <div class="rounded-xl overflow-hidden bg-white max-w-2xl" style="border: 1px solid hsl(220 10% 90%); box-shadow: 0 1px 4px hsl(220 15% 13% / 0.04);">
        <form action="{{ route('categories.update', $category) }}" method="POST" class="p-5">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label for="name" class="block text-xs font-bold mb-1.5" style="color: hsl(220 15% 30%);">Nama Kategori</label>
                <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required
                    class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50"
                    style="color: hsl(220 15% 13%);">
                @error('name')
                    <p class="text-[10px] text-red-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="sla_hours" class="block text-xs font-bold mb-1.5" style="color: hsl(220 15% 30%);">Target SLA Penanganan (Jam)</label>
                <div class="flex items-center gap-2">
                    <input type="number" name="sla_hours" id="sla_hours" value="{{ old('sla_hours', $category->sla_hours) }}" required min="1"
                        class="w-32 text-sm rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50"
                        style="color: hsl(220 15% 13%);">
                    <span class="text-xs font-medium" style="color: hsl(220 8% 55%);">Jam</span>
                </div>
                <p class="text-[10px] mt-1" style="color: hsl(220 8% 55%);">Waktu maksimal bagi armada untuk menangani laporan sejak diverifikasi.</p>
                @error('sla_hours')
                    <p class="text-[10px] text-red-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t flex justify-end" style="border-color: hsl(220 10% 90%);">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold text-white shadow-sm transition-transform hover:scale-105" style="background: hsl(168 78% 21%);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Perbarui Kategori
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
