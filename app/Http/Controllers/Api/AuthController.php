<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
    
        $user = User::create([
            'id' => (string) Str::uuid(), 
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
    
        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ]);
    }
    

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
    
        $user = User::where('email', $request->email)->first();
    
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email atau password salah.'], 401);
        }
    
        // Generate token via Sanctum
        $token = $user->createToken('api-token')->plainTextToken;
    
        // Simpan token juga ke kolom `users.token`
        $user->update([
            'token' => $token
        ]);
    
        return response()->json([
            'message' => 'Login berhasil',
            'user' => $user
        ]);
    }
    
}