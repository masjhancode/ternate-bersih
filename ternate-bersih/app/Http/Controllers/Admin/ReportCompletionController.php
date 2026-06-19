<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\ReportProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ReportCompletionController extends Controller
{
    public function index()
    {
        // Menampilkan laporan yang sedang ditugaskan dan menunggu diselesaikan
        $reports = Report::with(['category', 'user'])
            ->whereIn('status', ['Ditugaskan', 'Dalam Penanganan'])
            ->orderBy('updated_at', 'desc')
            ->paginate(12);

        return view('admin.reports.completions', compact('reports'));
    }

    public function complete(Request $request, Report $report)
    {
        // Pastikan laporan ada di status yang benar
        if (!in_array($report->status, ['Ditugaskan', 'Dalam Penanganan'])) {
            return back()->with('error', 'Laporan ini belum ditugaskan atau sudah diselesaikan.');
        }

        $request->validate([
            'photo_after' => 'required|image|mimes:jpg,jpeg,png|max:5120', // Max 5MB
            'notes' => 'required|string|max:1000'
        ]);

        // Simpan foto bukti selesai
        $path = $request->file('photo_after')->store('reports/after', 'public');

        // Update status master laporan
        $report->update([
            'status' => 'Selesai'
        ]);

        // Cari progres penugasan terakhir untuk mendapatkan armada yang dipakai
        $latestProgress = $report->progresses()->latest()->first();

        // Tambahkan riwayat penyelesaian
        ReportProgress::create([
            'report_id' => $report->id,
            'user_id' => Auth::id(),
            'fleet_id' => $latestProgress ? $latestProgress->fleet_id : null,
            'status' => 'Selesai',
            'photo_after_path' => $path,
            'notes' => $request->notes
        ]);

        // Kirim Notifikasi ke Admin
        $admins = \App\Models\User::where('role', 'Administrator')->get();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\ReportStatusUpdated(
            'Laporan Selesai', 
            "Laporan sampah #{$report->report_number} telah berhasil dibersihkan.", 
            url('/dashboard')
        ));

        return redirect()->route('admin.reports.completions')->with('success', 'Kerja Bagus! Laporan ' . $report->report_number . ' berhasil diselesaikan.');
    }
}
