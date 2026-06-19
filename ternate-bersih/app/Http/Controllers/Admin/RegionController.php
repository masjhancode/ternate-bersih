<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Village;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function index()
    {
        $districts = District::with('villages')->orderBy('name')->get();
        return view('admin.regions.index', compact('districts'));
    }

    public function storeDistrict(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:districts,name|max:255'
        ]);

        District::create($request->all());

        return back()->with('success', 'Kecamatan berhasil ditambahkan.');
    }

    public function updateDistrict(Request $request, District $district)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:districts,name,' . $district->id
        ]);

        $district->update($request->all());

        return back()->with('success', 'Kecamatan berhasil diperbarui.');
    }

    public function destroyDistrict(District $district)
    {
        try {
            $district->delete();
            return back()->with('success', 'Kecamatan berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Kecamatan tidak dapat dihapus karena masih memiliki data kelurahan atau riwayat yang terkait.');
        }
    }

    public function storeVillage(Request $request)
    {
        $request->validate([
            'district_id' => 'required|exists:districts,id',
            'name' => 'required|string|max:255'
        ]);

        Village::create($request->all());

        return back()->with('success', 'Kelurahan berhasil ditambahkan.');
    }

    public function updateVillage(Request $request, Village $village)
    {
        $request->validate([
            'district_id' => 'required|exists:districts,id',
            'name' => 'required|string|max:255'
        ]);

        $village->update($request->all());

        return back()->with('success', 'Kelurahan berhasil diperbarui.');
    }

    public function destroyVillage(Village $village)
    {
        try {
            $village->delete();
            return back()->with('success', 'Kelurahan berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Kelurahan tidak dapat dihapus karena sedang terkait dengan data lain.');
        }
    }
}
