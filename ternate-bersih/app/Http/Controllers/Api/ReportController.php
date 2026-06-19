<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // History laporan milik user yang login
        $reports = Report::with(['category'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $reports
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:report_categories,id',
            'photo' => 'required|image|max:5120', // Max 5MB
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'address' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $photoPath = $request->file('photo')->store('reports', 'public');
        $reportNumber = 'REP-' . date('YmdHis') . '-' . strtoupper(Str::random(4));

        $report = Report::create([
            'report_number' => $reportNumber,
            'user_id' => $request->user()->id,
            'category_id' => $request->category_id,
            'photo_path' => $photoPath,
            'location' => DB::raw("ST_SetSRID(ST_MakePoint({$request->lng}, {$request->lat}), 4326)"),
            'address' => $request->address,
            'description' => $request->description,
            'status' => 'Menunggu Verifikasi',
            'priority' => 'Sedang'
        ]);

        // Kirim Notifikasi Realtime ke Admin
        $admins = \App\Models\User::where('role', 'Administrator')->get();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\ReportStatusUpdated(
            'Laporan Baru Masuk (Mobile)', 
            'Terdapat aduan sampah baru via Mobile dari '.$request->user()->name, 
            url('/admin/reports/verifications')
        ));

        return response()->json([
            'status' => 'success',
            'message' => 'Laporan berhasil dikirim dan sedang menunggu verifikasi.',
            'data' => [
                'report_number' => $report->report_number
            ]
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $report = Report::with(['category', 'progresses.fleet', 'user'])
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$report) {
            return response()->json([
                'status' => 'error',
                'message' => 'Laporan tidak ditemukan atau Anda tidak memiliki akses.'
            ], 404);
        }

        // Ekstrak koordinat geometry PostGIS
        $location = DB::table('reports')
            ->select(DB::raw('ST_Y(location::geometry) as lat, ST_X(location::geometry) as lng'))
            ->where('id', $report->id)
            ->first();
            
        $report->lat = $location->lat ?? 0;
        $report->lng = $location->lng ?? 0;

        return response()->json([
            'status' => 'success',
            'data' => $report
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $report = Report::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$report) {
            return response()->json([
                'status' => 'error',
                'message' => 'Laporan tidak ditemukan.'
            ], 404);
        }

        if ($report->status !== 'Menunggu Verifikasi') {
            return response()->json([
                'status' => 'error',
                'message' => 'Laporan ini sudah diproses dan tidak dapat dihapus.'
            ], 403);
        }

        $report->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Laporan berhasil dihapus.'
        ]);
    }
}
