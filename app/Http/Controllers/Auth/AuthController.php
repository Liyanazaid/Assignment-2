<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // POST /api/auth/register
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return response()->json($user, 201);
    }

    // POST /api/auth/login
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token
        ], 200);
    }

    // GET /api/auth/me
    public function me(Request $request)
    {
        return response()->json($request->user(), 200);
    }

    // POST /api/auth/logout
   public function logout(Request $request)
    {
    // Get the current token
    /** @var \Laravel\Sanctum\PersonalAccessToken|null $token */
    $token = $request->user()->currentAccessToken();

    // If token exists, delete it
    $token?->delete();

    return response()->json(['message' => 'Logged out'], 200);
    }
}

