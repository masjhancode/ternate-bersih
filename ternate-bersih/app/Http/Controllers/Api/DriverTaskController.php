<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\ReportProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DriverTaskController extends Controller
{
    /**
     * Get tasks assigned to the logged-in driver's fleet.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'Driver Armada' || !$user->fleet) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized or Fleet not found for this driver.',
            ], 403);
        }

        $fleetId = $user->fleet->id;

        // Cari progress penugasan terakhir yang statusnya masih 'Ditugaskan' untuk armada ini
        $tasks = Report::select('*', \Illuminate\Support\Facades\DB::raw('ST_Y(location) as lat'), \Illuminate\Support\Facades\DB::raw('ST_X(location) as lng'))
            ->with(['category', 'user'])
            ->whereHas('progresses', function ($query) use ($fleetId) {
                $query->where('fleet_id', $fleetId)
                      ->where('status', 'Ditugaskan');
            })
            ->where('status', 'Ditugaskan') // Pastikan status laporan secara keseluruhan memang Ditugaskan
            ->orderBy('priority', 'asc') // Assuming Enum logic or simple string matching
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $tasks
        ]);
    }

    /**
     * Complete a task by uploading a proof photo.
     */
    public function complete(Request $request, $id)
    {
        $user = $request->user();

        if ($user->role !== 'Driver Armada' || !$user->fleet) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized or Fleet not found for this driver.',
            ], 403);
        }

        $report = Report::findOrFail($id);

        if ($report->status !== 'Ditugaskan') {
            return response()->json([
                'status' => 'error',
                'message' => 'Report is not in an assigned state.',
            ], 400);
        }

        $request->validate([
            'proof_photo' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'notes' => 'nullable|string'
        ]);

        $path = $request->file('proof_photo')->store('proofs', 'public');

        $report->update([
            'status' => 'Selesai'
        ]);

        ReportProgress::create([
            'report_id' => $report->id,
            'user_id' => $user->id,
            'fleet_id' => $user->fleet->id,
            'status' => 'Selesai',
            'notes' => $request->notes ?? 'Pembersihan telah diselesaikan oleh armada.',
            'photo_after_path' => $path
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Laporan berhasil diselesaikan.'
        ]);
    }

    /**
     * Get history of completed tasks by this fleet.
     */
    public function history(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'Driver Armada' || !$user->fleet) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized or Fleet not found for this driver.',
            ], 403);
        }

        $fleetId = $user->fleet->id;

        $history = Report::select('*', \Illuminate\Support\Facades\DB::raw('ST_Y(location) as lat'), \Illuminate\Support\Facades\DB::raw('ST_X(location) as lng'))
            ->with(['category', 'user'])
            ->whereHas('progresses', function ($query) use ($fleetId) {
                $query->where('fleet_id', $fleetId)
                      ->where('status', 'Selesai');
            })
            ->where('status', 'Selesai')
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $history
        ]);
    }
}
