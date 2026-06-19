<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Exports\ReportsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    public function index()
    {
        $reports = Report::with(['category', 'user'])->latest()->paginate(15);
        return view('admin.exports.index', compact('reports'));
    }

    public function exportExcel()
    {
        return Excel::download(new ReportsExport, 'Data_Pelaporan_Sampah_Ternate.xlsx');
    }

    public function exportPdf()
    {
        $reports = Report::with(['category', 'user'])->latest()->get();
        
        $pdf = Pdf::loadView('admin.exports.pdf', compact('reports'))
                  ->setPaper('a4', 'landscape');
                  
        return $pdf->download('Rekap_Laporan_Sampah_Ternate.pdf');
    }
}
