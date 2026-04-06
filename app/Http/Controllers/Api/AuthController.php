<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $key = 'login:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'message' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik."
            ], 429);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            RateLimiter::hit($key, 60); // blokir 60 detik setelah 5 gagal
            return response()->json(['message' => 'Email atau password salah'], 401);
        }

        RateLimiter::clear($key);

        return $this->respondWithToken($token);
    }

    public function me()
    {
        return response()->json(auth('api')->user());
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Berhasil logout']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    public function updateProfile(Request $request)
    {
        $user = auth('api')->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'no_hp' => 'sometimes|string|max:20',
            'foto' => 'sometimes|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'no_hp']);

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('foto-profil', 'public');
            $data['foto'] = $path;
        }

        if ($request->filled('password')) {
            $request->validate([
                'password_lama' => 'required',
                'password' => 'required|min:8|confirmed',
            ]);

            if (!Hash::check($request->password_lama, $user->password)) {
                return response()->json(['message' => 'Password lama tidak sesuai'], 422);
            }

            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json(['message' => 'Profil berhasil diupdate', 'user' => $user->fresh()]);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth('api')->user(),
        ]);
    }
}
