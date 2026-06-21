<?php

// File: app/Http/Controllers/Api/AuthController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login: Menerima email & password, mengembalikan token
     *
     * @param Request $request (data dari frontend)
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        // Validasi input: email wajib, password wajib
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            // Pesan error dalam Bahasa Indonesia
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        // Cek: apakah user tidak ditemukan ATAU password salah?
        // Hash::check() = membandingkan password input dengan password terenkripsi di database
        if (!$user || !Hash::check($request->password, $user->password)) {
            // Lempar error validasi
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        // Hapus token lama user (biar tidak menumpuk)
        $user->tokens()->delete();

        // Buat token baru dengan Sanctum
        // createToken('nama_token') -> 'kkn-token' adalah nama bebas
        // plainTextToken -> token dalam bentuk string yang akan dikirim ke frontend
        $token = $user->createToken('kkn-token')->plainTextToken;

        // Kirim response sukses dengan format konsisten
        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'user'  => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                ],
                'token' => $token,
            ],
        ]);
    }

    /**
     * Logout: Menghapus token yang sedang digunakan
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        // Menghapus token yang dipakai oleh user saat ini
        // currentAccessToken() = token yang digunakan di request ini
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
            'data'    => null,
        ]);
    }
}