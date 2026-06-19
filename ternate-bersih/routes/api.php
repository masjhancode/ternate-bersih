<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\MasterDataController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/master/categories', [MasterDataController::class, 'categories']);
Route::get('/master/regions', [MasterDataController::class, 'regions']);

// Endpoint untuk auto-refresh dashboard admin (polling)
Route::get('/dashboard-stats', function () {
    $total = \App\Models\Report::count();
    $waiting = \App\Models\Report::where('status', 'Menunggu Verifikasi')->count();
    $processed = \App\Models\Report::whereIn('status', ['Diverifikasi', 'Dalam Penanganan', 'Ditugaskan'])->count();
    $completed = \App\Models\Report::where('status', 'Selesai')->count();
    $rejected = \App\Models\Report::where('status', 'Ditolak')->count();
    
    // Checksum digunakan JS untuk mendeteksi ADA perubahan apa pun
    $checksum = $total + $waiting + $processed + $completed + $rejected;

    return response()->json([
        'totalReports' => $total,
        'waitingVerification' => $waiting,
        'processed' => $processed,
        'completed' => $completed,
        'rejected' => $rejected,
        'checksum' => $checksum,
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/notifications', [AuthController::class, 'notifications']);
    Route::post('/notifications/{id}/read', [AuthController::class, 'markNotificationRead']);

    // Citizen Reports
    Route::get('/reports', [ReportController::class, 'index']);
    Route::post('/reports', [ReportController::class, 'store']);
    Route::get('/reports/{id}', [ReportController::class, 'show']);
    Route::delete('/reports/{id}', [ReportController::class, 'destroy']);

    // Driver Tasks API
    Route::get('/driver/tasks', [\App\Http\Controllers\Api\DriverTaskController::class, 'index']);
    Route::get('/driver/tasks/history', [\App\Http\Controllers\Api\DriverTaskController::class, 'history']);
    Route::post('/driver/tasks/{id}/complete', [\App\Http\Controllers\Api\DriverTaskController::class, 'complete']);
});
