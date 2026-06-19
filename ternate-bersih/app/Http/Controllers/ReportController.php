<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'aktif');
        $year = $request->get('year', date('Y'));

        $query = Report::with(['category', 'user']);

        if (auth()->user()->role !== 'Administrator') {
            $query->where('user_id', auth()->id());
        }

        if ($tab === 'riwayat') {
            // Tab Riwayat: Hanya yang Selesai / Ditolak
            $query->whereIn('status', ['Selesai', 'Ditolak']);
            if ($year) {
                $query->whereYear('created_at', $year);
            }
            $reports = $query->latest()->paginate(150);
        } else {
            // Tab Aktif: Laporan yang sedang berjalan
            $query->whereNotIn('status', ['Selesai', 'Ditolak']);
            $reports = $query->latest()->paginate(20);
        }
        
        return view('reports.index', compact('reports', 'tab', 'year'));
    }

    public function create()
    {
        $categories = ReportCategory::all();
        return view('reports.create', compact('categories'));
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
            'user_id' => auth()->id(),
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
            'Laporan Baru Masuk', 
            'Terdapat aduan sampah baru dari '.auth()->user()->name, 
            route('admin.reports.verifications')
        ));

        return redirect()->route('reports.index')->with('success', 'Laporan berhasil dikirim dan sedang menunggu verifikasi.');
    }

    public function show(Report $report)
    {
        // Pastikan masyarakat hanya bisa melihat laporannya sendiri
        if (auth()->user()->role === 'Masyarakat' && $report->user_id !== auth()->id()) {
            abort(403);
        }
        
        $report->load(['category', 'user', 'progresses.fleet']);
        
        // Mengambil koordinat dari tipe geometry PostgreSQL PostGIS
        $location = DB::table('reports')
            ->select(DB::raw('ST_Y(location::geometry) as lat, ST_X(location::geometry) as lng'))
            ->where('id', $report->id)
            ->first();
            
        $report->lat = $location->lat ?? 0;
        $report->lng = $location->lng ?? 0;

        return view('reports.show', compact('report'));
    }
}
