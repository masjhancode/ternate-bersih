<x-app-layout>
    <x-slot name="header">
        <h2 class="text-sm font-bold" style="color: hsl(220 15% 13%);">
            {{ Auth::user()->role === 'Administrator' ? 'Semua Laporan Masuk' : 'Riwayat Laporan Saya' }}
        </h2>
    </x-slot>

    <div class="mb-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h3 class="text-base font-bold" style="color: hsl(220 15% 13%);">Daftar Laporan Sampah</h3>
            <p class="text-xs" style="color: hsl(220 8% 55%);">Pantau status penanganan tumpukan sampah yang telah dilaporkan.</p>
        </div>
        
        <div class="flex items-center gap-2 flex-wrap">
            <!-- Navigation Tabs -->
            <div class="flex bg-gray-100 p-1 rounded-lg">
                <a href="{{ route('reports.index', ['tab' => 'aktif']) }}" class="px-4 py-1.5 text-xs font-bold rounded-md transition-colors {{ $tab == 'aktif' ? 'bg-white shadow-sm text-emerald-700' : 'text-gray-500 hover:text-gray-700' }}">
                    Aktif
                </a>
                <a href="{{ route('reports.index', ['tab' => 'riwayat']) }}" class="px-4 py-1.5 text-xs font-bold rounded-md transition-colors {{ $tab == 'riwayat' ? 'bg-white shadow-sm text-emerald-700' : 'text-gray-500 hover:text-gray-700' }}">
                    Riwayat
                </a>
            </div>

            @if($tab === 'riwayat')
            <!-- Filter Tahun -->
            <form action="{{ route('reports.index') }}" method="GET" class="flex items-center m-0">
                <input type="hidden" name="tab" value="riwayat">
                <select name="year" onchange="this.form.submit()" class="text-xs border-gray-300 rounded-lg py-1.5 pl-3 pr-8 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">Semua Tahun</option>
                    @for($i = date('Y'); $i >= 2020; $i--)
                        <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </form>
            @endif

            <a href="{{ route('reports.create') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold text-white shadow-sm transition-transform hover:scale-105" style="background: hsl(168 78% 21%);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Laporan Baru
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 rounded-lg flex items-center gap-2 text-xs font-bold" style="background: hsl(168 42% 92%); color: hsl(168 78% 21%); border: 1px solid hsl(168 42% 86%);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if($tab === 'riwayat')
        <!-- Tampilan Tabel Riwayat -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-4">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-[10px] uppercase tracking-wider border-b border-gray-200">
                            <th class="p-3 font-bold">No. Laporan</th>
                            <th class="p-3 font-bold">Tanggal</th>
                            <th class="p-3 font-bold">Kategori</th>
                            <th class="p-3 font-bold">Lokasi</th>
                            @if(Auth::user()->role === 'Administrator')
                                <th class="p-3 font-bold">Pelapor</th>
                            @endif
                            <th class="p-3 font-bold text-center">Status</th>
                            <th class="p-3 font-bold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs text-gray-700">
                        @forelse($reports as $report)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="p-3 font-bold" style="color: hsl(168 78% 21%);">{{ $report->report_number }}</td>
                                <td class="p-3">{{ $report->created_at->format('d M Y H:i') }}</td>
                                <td class="p-3 font-medium">{{ $report->category->name }}</td>
                                <td class="p-3 truncate max-w-xs" title="{{ $report->address }}">{{ $report->address }}</td>
                                @if(Auth::user()->role === 'Administrator')
                                    <td class="p-3 truncate max-w-[120px]" title="{{ $report->user->name }}">{{ $report->user->name }}</td>
                                @endif
                                <td class="p-3 text-center">
                                    @php
                                        $badgeStyle = 'background: hsl(0 84% 95%); color: hsl(0 84% 50%);'; // Ditolak
                                        if ($report->status == 'Selesai') $badgeStyle = 'background: hsl(168 42% 92%); color: hsl(168 78% 21%);';
                                    @endphp
                                    <span class="inline-flex px-2 py-1 rounded text-[10px] font-bold" style="{{ $badgeStyle }}">
                                        {{ $report->status }}
                                    </span>
                                </td>
                                <td class="p-3 text-center">
                                    <a href="{{ route('reports.show', $report) }}" class="font-bold hover:underline" style="color: hsl(168 78% 21%);">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ Auth::user()->role === 'Administrator' ? '7' : '6' }}" class="p-6 text-center text-gray-500">
                                    Belum ada riwayat laporan di periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Tampilan Card Laporan Aktif -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($reports as $report)
                <div class="rounded-xl overflow-hidden bg-white flex flex-col" style="border: 1px solid hsl(220 10% 90%); box-shadow: 0 1px 4px hsl(220 15% 13% / 0.04);">
                    <div class="h-40 overflow-hidden relative bg-gray-100">
                        <img src="{{ Storage::url($report->photo_path) }}" alt="Foto Sampah" class="w-full h-full object-cover">
                        
                        @php
                            $badgeStyle = 'background: hsl(45 93% 92%); color: hsl(45 93% 35%);'; // Menunggu Verifikasi
                            if ($report->status == 'Selesai') $badgeStyle = 'background: hsl(168 42% 92%); color: hsl(168 78% 21%);';
                            elseif (in_array($report->status, ['Dalam Penanganan', 'Ditugaskan', 'Diverifikasi'])) $badgeStyle = 'background: hsl(226 100% 95%); color: hsl(226 100% 50%);';
                            elseif ($report->status == 'Ditolak') $badgeStyle = 'background: hsl(0 84% 95%); color: hsl(0 84% 50%);';
                        @endphp
                        
                        <span class="absolute top-3 left-3 px-2 py-1 rounded text-[10px] font-bold shadow-sm" style="{{ $badgeStyle }} backdrop-filter: blur(4px);">
                            {{ $report->status }}
                        </span>
                        <span class="absolute top-3 right-3 px-2 py-1 bg-black/60 text-white rounded text-[10px] font-bold backdrop-blur-sm shadow-sm">
                            {{ $report->category->name }}
                        </span>
                    </div>
                    <div class="p-4 flex-1 flex flex-col">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="text-sm font-bold truncate" style="color: hsl(220 15% 13%);">{{ $report->report_number }}</h4>
                            <span class="text-[10px] whitespace-nowrap" style="color: hsl(220 8% 55%);">{{ $report->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs line-clamp-2 mb-3 flex-1" style="color: hsl(220 8% 45%);">{{ $report->address }}</p>
                        
                        @if(Auth::user()->role === 'Administrator')
                        <p class="text-[10px] mb-3 font-semibold" style="color: hsl(220 8% 55%);">Pelapor: {{ $report->user->name }}</p>
                        @endif

                        <div class="pt-3 border-t mt-auto" style="border-color: hsl(220 10% 92%);">
                            <a href="{{ route('reports.show', $report) }}" class="block text-center w-full py-1.5 rounded-md text-xs font-bold transition-colors" style="background: hsl(220 10% 96%); color: hsl(168 78% 21%); hover:background: hsl(168 42% 92%);">
                                Lihat Detail Laporan
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center rounded-xl" style="background: white; border: 1px dashed hsl(220 10% 85%);">
                    <svg class="mx-auto h-12 w-12 mb-3" style="color: hsl(220 10% 80%);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <h3 class="text-sm font-bold" style="color: hsl(220 15% 30%);">Belum Ada Laporan Aktif</h3>
                    <p class="text-xs mt-1" style="color: hsl(220 8% 55%);">Tidak ada laporan yang sedang berjalan.</p>
                </div>
            @endforelse
        </div>
    @endif

    <div class="mt-4 mb-8">
        {{ $reports->appends(['tab' => $tab, 'year' => $year])->links() }}
    </div>
</x-app-layout>
