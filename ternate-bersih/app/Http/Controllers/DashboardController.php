<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik Laporan (Global)
        $totalReports = Report::count();
        $waitingVerification = Report::where('status', 'Menunggu Verifikasi')->count();
        $inProgress = Report::whereIn('status', ['Diverifikasi', 'Ditugaskan', 'Dalam Penanganan'])->count();
        $completed = Report::where('status', 'Selesai')->count();

        // Data Laporan Terbaru
        $recentReports = Report::with(['category', 'user'])
            ->latest()
            ->take(5)
            ->get();

        // Data untuk Map (Hanya laporan yang aktif/belum selesai dan tidak ditolak)
        $mapReports = Report::whereNotIn('status', ['Selesai', 'Ditolak'])
            ->select(
                'id', 
                'report_number', 
                'status', 
                'address', 
                \Illuminate\Support\Facades\DB::raw('ST_Y(location::geometry) as lat'), 
                \Illuminate\Support\Facades\DB::raw('ST_X(location::geometry) as lng')
            )
            ->get();

        return view('dashboard', compact(
            'totalReports', 
            'waitingVerification', 
            'inProgress', 
            'completed', 
            'recentReports',
            'mapReports'
        ));
    }
}
