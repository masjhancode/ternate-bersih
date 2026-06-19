<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // Halaman khusus untuk daftar laporan yang Menunggu Verifikasi
    public function verifications()
    {
        $reports = Report::with(['category', 'user'])
            ->where('status', 'Menunggu Verifikasi')
            ->orderBy('created_at', 'asc')
            ->paginate(12);
            
        return view('admin.reports.verifications', compact('reports'));
    }

    // Proses aksi Verifikasi (Terima atau Tolak)
    public function verify(Request $request, Report $report)
    {
        // Pastikan laporan masih dalam status menunggu verifikasi
        if ($report->status !== 'Menunggu Verifikasi') {
            return back()->with('error', 'Laporan ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'action' => 'required|in:terima,tolak',
            'priority' => 'required_if:action,terima|in:Rendah,Sedang,Tinggi',
            // Catatan biasanya opsional jika diterima, namun wajib jika ditolak
            'description' => 'required_if:action,tolak|string|nullable'
        ]);

        if ($request->action === 'terima') {
            $report->update([
                'status' => 'Diverifikasi',
                'priority' => $request->priority
            ]);
            $message = "Laporan berhasil diverifikasi dan siap ditugaskan ke armada.";

            // Kirim Push Notification ke pelapor (Masyarakat)
            $this->sendFcmPush(
                $report->user, 
                'Laporan Diproses! 🚀', 
                "Laporan Anda (REP-{$report->id}) telah diverifikasi dan masuk antrean penugasan armada."
            );

            // Kirim Notifikasi Internal Admin
            $admins = \App\Models\User::where('role', 'Administrator')->get();
            \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\ReportStatusUpdated(
                'Laporan Diverifikasi', 
                "Laporan #{$report->report_number} telah diverifikasi dengan prioritas {$request->priority}.", 
                route('admin.reports.assignments')
            ));
        } else {
            $report->update([
                'status' => 'Ditolak',
                // Kita update deskripsi laporan dengan alasan penolakan
                // atau idealnya ada kolom khusus, tapi untuk kesederhanaan kita append ke deskripsi
                'description' => $report->description . "\n\n[DITOLAK ADMIN]: " . $request->description
            ]);
            $message = "Laporan berhasil ditolak.";

            // Kirim Push Notification ke pelapor (Masyarakat)
            $this->sendFcmPush(
                $report->user, 
                'Laporan Ditolak ❌', 
                "Maaf, laporan Anda (REP-{$report->id}) tidak dapat kami proses: " . $request->description
            );

            // Kirim Notifikasi Internal Admin
            $admins = \App\Models\User::where('role', 'Administrator')->get();
            \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\ReportStatusUpdated(
                'Laporan Ditolak', 
                "Laporan #{$report->report_number} telah ditolak.", 
                '#'
            ));
        }

        return redirect()->route('admin.reports.verifications')->with('success', $message);
    }

    private function sendFcmPush($user, $title, $body)
    {
        if ($user && $user->fcm_token) {
            try {
                $messaging = app('firebase.messaging');
                $message = \Kreait\Firebase\Messaging\CloudMessage::new()
                    ->withToken($user->fcm_token)
                    ->withNotification(\Kreait\Firebase\Messaging\Notification::create($title, $body));
                $messaging->send($message);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('FCM Error: ' . $e->getMessage());
            }
        }
    }
}
