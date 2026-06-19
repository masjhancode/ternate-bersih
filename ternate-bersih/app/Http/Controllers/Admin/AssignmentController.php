<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Fleet;
use App\Models\ReportProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    public function index()
    {
        // Memasukkan data armada dummy jika masih kosong (untuk keperluan demo)
        if (Fleet::count() === 0) {
            Fleet::insert([
                ['plate_number' => 'DG 8001 A', 'vehicle_type' => 'Truk Sampah Besar', 'capacity' => '6 Ton'],
                ['plate_number' => 'DG 8002 B', 'vehicle_type' => 'Pick Up', 'capacity' => '2 Ton'],
                ['plate_number' => 'DG 8003 C', 'vehicle_type' => 'Motor Roda Tiga', 'capacity' => '500 Kg'],
            ]);
        }

        $reports = Report::with(['category', 'user'])
            ->whereIn('status', ['Diverifikasi', 'Ditugaskan'])
            // Sorting berdasarkan prioritas kompatibel dengan PostgreSQL
            ->orderByRaw("
                CASE priority 
                    WHEN 'Tinggi' THEN 1 
                    WHEN 'Sedang' THEN 2 
                    WHEN 'Rendah' THEN 3 
                    ELSE 4 
                END
            ")
            ->orderBy('created_at', 'asc')
            ->paginate(12);
            
        $fleets = Fleet::all();

        return view('admin.reports.assignments', compact('reports', 'fleets'));
    }

    public function assign(Request $request, Report $report)
    {
        // Pastikan laporan sudah diverifikasi
        if (!in_array($report->status, ['Diverifikasi', 'Ditugaskan'])) {
            return back()->with('error', 'Laporan belum diverifikasi atau sudah selesai.');
        }

        $request->validate([
            'fleet_id' => 'required|exists:fleets,id',
            'notes' => 'nullable|string'
        ]);

        $report->update([
            'status' => 'Ditugaskan'
        ]);

        ReportProgress::create([
            'report_id' => $report->id,
            'user_id' => Auth::id(), // Petugas yang menugaskan
            'fleet_id' => $request->fleet_id,
            'status' => 'Ditugaskan',
            'notes' => $request->notes ?? 'Armada telah ditugaskan untuk mengangkut tumpukan sampah di lokasi.'
        ]);

        $fleet = Fleet::find($request->fleet_id);

        // Kirim Notifikasi ke Admin
        $admins = \App\Models\User::where('role', 'Administrator')->get();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\ReportStatusUpdated(
            'Armada Ditugaskan', 
            "Armada {$fleet->plate_number} ditugaskan untuk laporan #{$report->report_number}.", 
            route('admin.reports.completions')
        ));

        return redirect()->route('admin.reports.assignments')->with('success', 'Armada berhasil ditugaskan untuk menangani laporan ' . $report->report_number);
    }
}
