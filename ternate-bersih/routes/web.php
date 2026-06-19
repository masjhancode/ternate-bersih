<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $stats = [
        'total_reports' => \App\Models\Report::count(),
        'completed_reports' => \App\Models\Report::where('status', 'Selesai')->count(),
        'fleets_active' => \App\Models\Fleet::count(),
    ];
    
    $recent_reports = \App\Models\Report::with(['category'])
        ->orderBy('created_at', 'desc')
        ->take(6)
        ->get();
        
    // Grafik Harian (14 Hari Terakhir)
    $daily_trends = \App\Models\Report::select(
        \Illuminate\Support\Facades\DB::raw("TO_CHAR(created_at, 'DD Mon') as period"),
        \Illuminate\Support\Facades\DB::raw('count(*) as count')
    )
    ->where('created_at', '>=', now()->subDays(13)->startOfDay())
    ->groupBy('period')
    ->orderBy(\Illuminate\Support\Facades\DB::raw("MIN(created_at)"))
    ->get();

    // Grafik Bulanan (12 Bulan Terakhir)
    $monthly_trends = \App\Models\Report::select(
        \Illuminate\Support\Facades\DB::raw("TO_CHAR(created_at, 'Mon YYYY') as period"),
        \Illuminate\Support\Facades\DB::raw('count(*) as count')
    )
    ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
    ->groupBy('period')
    ->orderBy(\Illuminate\Support\Facades\DB::raw("MIN(created_at)"))
    ->get();

    // Grafik Tahunan (Seluruh Waktu)
    $yearly_trends = \App\Models\Report::select(
        \Illuminate\Support\Facades\DB::raw("TO_CHAR(created_at, 'YYYY') as period"),
        \Illuminate\Support\Facades\DB::raw('count(*) as count')
    )
    ->groupBy('period')
    ->orderBy('period', 'asc')
    ->get();

    return view('welcome', compact('stats', 'recent_reports', 'daily_trends', 'monthly_trends', 'yearly_trends'));
});

Route::get('/laporan-publik/{id}', function ($id) {
    $report = \App\Models\Report::with(['category', 'user', 'progresses' => function($q) {
        $q->orderBy('created_at', 'desc');
    }])->findOrFail($id);
    
    return view('public_report', compact('report'));
})->name('public.report.show');

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/guide', function () {
    return view('guide');
})->middleware(['auth', 'verified'])->name('guide');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // AJAX Notifications
    Route::get('/notifications', function() {
        if (auth()->user()->role !== 'Administrator') return response()->json([]);
        
        $notifications = auth()->user()->notifications()->take(10)->get()->map(function($notif) {
            $notif->created_at_human = $notif->created_at->diffForHumans();
            return $notif;
        });
        return response()->json($notifications);
    })->name('notifications.index');

    Route::post('/notifications/mark-as-read', function() {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    })->name('notifications.markAsRead');
    
    // Master Data
    Route::resource('categories', \App\Http\Controllers\Admin\ReportCategoryController::class)->except(['show']);
    Route::resource('admin/fleets', \App\Http\Controllers\Admin\FleetController::class, ['as' => 'admin'])->except(['show', 'create', 'edit']);
    
    // Manajemen Wilayah (Kecamatan & Kelurahan)
    Route::get('admin/regions', [\App\Http\Controllers\Admin\RegionController::class, 'index'])->name('admin.regions.index');
    Route::post('admin/regions/districts', [\App\Http\Controllers\Admin\RegionController::class, 'storeDistrict'])->name('admin.regions.districts.store');
    Route::put('admin/regions/districts/{district}', [\App\Http\Controllers\Admin\RegionController::class, 'updateDistrict'])->name('admin.regions.districts.update');
    Route::delete('admin/regions/districts/{district}', [\App\Http\Controllers\Admin\RegionController::class, 'destroyDistrict'])->name('admin.regions.districts.destroy');
    
    Route::post('admin/regions/villages', [\App\Http\Controllers\Admin\RegionController::class, 'storeVillage'])->name('admin.regions.villages.store');
    Route::put('admin/regions/villages/{village}', [\App\Http\Controllers\Admin\RegionController::class, 'updateVillage'])->name('admin.regions.villages.update');
    Route::delete('admin/regions/villages/{village}', [\App\Http\Controllers\Admin\RegionController::class, 'destroyVillage'])->name('admin.regions.villages.destroy');

    // Pengguna & Hak Akses
    Route::resource('admin/users', \App\Http\Controllers\Admin\UserController::class, ['as' => 'admin'])->except(['show', 'create', 'edit']);

    // Laporan & Ekspor Statistik
    Route::get('admin/exports', [\App\Http\Controllers\Admin\ExportController::class, 'index'])->name('admin.exports.index');
    Route::get('admin/exports/excel', [\App\Http\Controllers\Admin\ExportController::class, 'exportExcel'])->name('admin.exports.excel');
    Route::get('admin/exports/pdf', [\App\Http\Controllers\Admin\ExportController::class, 'exportPdf'])->name('admin.exports.pdf');

    // Modul Geografis (GIS)
    Route::get('admin/gis', [\App\Http\Controllers\Admin\GisController::class, 'index'])->name('admin.gis.index');


    
    // Verifikasi Laporan (Admin)
    Route::get('admin/reports/verifications', [\App\Http\Controllers\Admin\ReportController::class, 'verifications'])->name('admin.reports.verifications');
    Route::post('admin/reports/{report}/verify', [\App\Http\Controllers\Admin\ReportController::class, 'verify'])->name('admin.reports.verify');

    // Penugasan Armada (Admin)
    Route::get('admin/reports/assignments', [\App\Http\Controllers\Admin\AssignmentController::class, 'index'])->name('admin.reports.assignments');
    Route::post('admin/reports/{report}/assign', [\App\Http\Controllers\Admin\AssignmentController::class, 'assign'])->name('admin.reports.assign');

    // Penyelesaian Laporan (Admin/Petugas)
    Route::get('admin/reports/completions', [\App\Http\Controllers\Admin\ReportCompletionController::class, 'index'])->name('admin.reports.completions');
    Route::post('admin/reports/{report}/complete', [\App\Http\Controllers\Admin\ReportCompletionController::class, 'complete'])->name('admin.reports.complete');

    // Pelaporan Sampah (Umum)
    Route::resource('reports', \App\Http\Controllers\ReportController::class)->only(['index', 'create', 'store', 'show']);
});

require __DIR__.'/auth.php';
