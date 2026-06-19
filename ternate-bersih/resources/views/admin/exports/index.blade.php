<x-app-layout>
    <x-slot name="header">
        <h2 class="text-sm font-bold" style="color: hsl(220 15% 13%);">Laporan & Statistik</h2>
    </x-slot>

    <div class="mb-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h3 class="text-base font-bold" style="color: hsl(220 15% 13%);">Daftar Rekapitulasi Laporan</h3>
            <p class="text-xs" style="color: hsl(220 8% 55%);">Pantau seluruh riwayat laporan sampah dan unduh datanya untuk keperluan evaluasi.</p>
        </div>
        
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.exports.excel') }}" class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-transparent px-3 py-1.5 text-xs font-bold shadow-sm transition-colors hover:opacity-90" style="background: hsl(142 71% 25%); color: white;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Ekspor Excel
            </a>
            <a href="{{ route('admin.exports.pdf') }}" target="_blank" class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-transparent px-3 py-1.5 text-xs font-bold shadow-sm transition-colors hover:opacity-90" style="background: hsl(0 84% 50%); color: white;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3 3v-6"/></svg>
                Ekspor PDF
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl overflow-hidden" style="border: 1px solid hsl(220 10% 90%); box-shadow: 0 1px 4px hsl(220 15% 13% / 0.04);">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr style="background: hsl(220 10% 97%); border-bottom: 1px solid hsl(220 10% 90%);">
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Nomor Laporan</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Tanggal</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Pelapor</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Kategori</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Prioritas</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($reports as $report)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 font-bold" style="color: hsl(220 15% 13%);">
                                <a href="{{ route('reports.show', $report) }}" class="hover:text-emerald-600 hover:underline" target="_blank">{{ $report->report_number }}</a>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $report->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-4 py-3 text-xs text-gray-700">{{ $report->user->name ?? 'Anonim' }}</td>
                            <td class="px-4 py-3 text-xs text-gray-700">{{ $report->category->name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $prioColor = match($report->priority) {
                                        'Tinggi' => 'bg-red-100 text-red-700',
                                        'Sedang' => 'bg-amber-100 text-amber-700',
                                        'Rendah' => 'bg-blue-100 text-blue-700',
                                        default => 'bg-gray-100 text-gray-700'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $prioColor }}">
                                    {{ $report->priority }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusColor = match($report->status) {
                                        'Menunggu Verifikasi' => 'bg-amber-100 text-amber-700',
                                        'Diverifikasi', 'Ditugaskan', 'Dalam Penanganan' => 'bg-blue-100 text-blue-700',
                                        'Selesai' => 'bg-emerald-100 text-emerald-700',
                                        'Ditolak' => 'bg-red-100 text-red-700',
                                        default => 'bg-gray-100 text-gray-700'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $statusColor }}">
                                    {{ $report->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-xs text-gray-500">Belum ada riwayat laporan yang tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4">
        {{ $reports->links() }}
    </div>

</x-app-layout>
