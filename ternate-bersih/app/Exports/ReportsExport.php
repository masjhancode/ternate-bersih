<?php

namespace App\Exports;

use App\Models\Report;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Report::with(['category', 'user'])->latest()->get();
    }

    public function headings(): array
    {
        return [
            'Nomor Laporan',
            'Tanggal Lapor',
            'Pelapor',
            'Kategori',
            'Alamat',
            'Prioritas',
            'Status'
        ];
    }

    public function map($report): array
    {
        return [
            $report->report_number,
            $report->created_at->format('d/m/Y H:i'),
            $report->user->name ?? 'Anonim',
            $report->category->name ?? 'Lainnya',
            $report->address,
            $report->priority,
            $report->status
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Bold header
            1    => ['font' => ['bold' => true]],
        ];
    }
}
