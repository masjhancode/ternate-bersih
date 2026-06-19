<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'nik' => 'required|string|max:16|unique:users',
            'phone_number' => 'required|string|max:15',
            'village_id' => 'required|exists:villages,id',
            'address' => 'required|string'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'Masyarakat',
            'nik' => $request->nik,
            'phone_number' => $request->phone_number,
            'village_id' => $request->village_id,
            'address' => $request->address
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi berhasil',
            'data' => [
                'user' => $user->load('village'),
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Kredensial yang diberikan tidak cocok dengan data kami.'],
            ]);
        }

        if ($request->has('fcm_token')) {
            $user->fcm_token = $request->fcm_token;
            $user->save();
        }

        // Hapus token lama agar hanya bisa login di 1 device (opsional)
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'data' => [
                'user' => $user->load('village'),
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => $request->user()->load('village.district', 'fleet')
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil logout'
        ]);
    }

    public function notifications(Request $request)
    {
        $notifications = $request->user()->notifications()->get()->map(function($notif) {
            return [
                'id' => $notif->id,
                'type' => class_basename($notif->type),
                'data' => $notif->data,
                'read_at' => $notif->read_at,
                'created_at' => $notif->created_at->diffForHumans()
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $notifications
        ]);
    }

    public function markNotificationRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json([
            'status' => 'success'
        ]);
    }
}
