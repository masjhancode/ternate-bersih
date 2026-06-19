<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fleet;
use Illuminate\Http\Request;

class FleetController extends Controller
{
    public function index()
    {
        $fleets = Fleet::with('user')->latest()->paginate(10);
        $drivers = \App\Models\User::where('role', 'Driver Armada')->get();
        return view('admin.fleets.index', compact('fleets', 'drivers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'plate_number' => 'required|string|unique:fleets,plate_number',
            'vehicle_type' => 'required|string',
            'capacity' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id'
        ]);

        Fleet::create($request->all());

        return redirect()->route('admin.fleets.index')->with('success', 'Data armada berhasil ditambahkan.');
    }

    public function update(Request $request, Fleet $fleet)
    {
        $request->validate([
            'plate_number' => 'required|string|unique:fleets,plate_number,' . $fleet->id,
            'vehicle_type' => 'required|string',
            'capacity' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id'
        ]);

        $fleet->update($request->all());

        return redirect()->route('admin.fleets.index')->with('success', 'Data armada berhasil diperbarui.');
    }

    public function destroy(Fleet $fleet)
    {
        try {
            $fleet->delete();
            return redirect()->route('admin.fleets.index')->with('success', 'Data armada berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.fleets.index')->with('error', 'Armada tidak dapat dihapus karena sedang terkait dengan riwayat laporan.');
        }
    }
}
