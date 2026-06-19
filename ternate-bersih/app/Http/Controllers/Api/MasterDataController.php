<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\ReportCategory;

class MasterDataController extends Controller
{
    public function categories()
    {
        $categories = ReportCategory::orderBy('name')->get();
        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    public function regions()
    {
        $districts = District::with('villages')->orderBy('name')->get();
        return response()->json([
            'status' => 'success',
            'data' => $districts
        ]);
    }
}
