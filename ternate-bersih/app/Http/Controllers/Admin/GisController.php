<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GisController extends Controller
{
    public function index(Request $request)
    {
        $statusFilter = $request->query('status', 'Semua');

        $query = Report::select(
                'id', 
                'report_number', 
                'status', 
                'priority',
                'address', 
                DB::raw('ST_Y(location::geometry) as lat'), 
                DB::raw('ST_X(location::geometry) as lng')
            );

        if ($statusFilter !== 'Semua') {
            if ($statusFilter === 'Aktif') {
                $query->whereNotIn('status', ['Selesai', 'Ditolak']);
            } else {
                $query->where('status', $statusFilter);
            }
        }

        $reports = $query->get();

        return view('admin.gis.index', compact('reports', 'statusFilter'));
    }
}
