<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->paginate(12);
        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['Administrator', 'Operator DLH', 'Koordinator Lapangan', 'Petugas Lapangan', 'Driver Armada', 'Masyarakat'])],
            'nik' => 'nullable|string|max:16|unique:users',
            'phone_number' => 'nullable|string|max:15',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'nik' => $request->nik,
            'phone_number' => $request->phone_number,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['Administrator', 'Operator DLH', 'Koordinator Lapangan', 'Petugas Lapangan', 'Driver Armada', 'Masyarakat'])],
            'nik' => ['nullable', 'string', 'max:16', Rule::unique('users')->ignore($user->id)],
            'phone_number' => 'nullable|string|max:15',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'nik' => $request->nik,
            'phone_number' => $request->phone_number,
        ];

        // Jika form password diisi, maka update password
        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna dan hak akses berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Mencegah admin menghapus dirinya sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tindakan ditolak! Anda tidak dapat menghapus akun Anda sendiri.');
        }

        try {
            $user->delete();
            return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Pengguna tidak dapat dihapus karena masih terhubung dengan data laporan.');
        }
    }
}
