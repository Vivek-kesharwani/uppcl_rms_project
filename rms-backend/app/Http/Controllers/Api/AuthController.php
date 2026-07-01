<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid login credentials'
            ], 401);
        }

        $user = Auth::user();
        $user->load('domain', 'systemRole');

        $token = $user->createToken('rms_auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,

                // legacy role, keep for backward compatibility
                'role' => $user->role,

                // new enterprise identity fields
                'domain' => $user->domain?->name,
                'system_role' => $user->systemRole?->name,
            ]
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $user->load('domain', 'systemRole');

        return response()->json([
            'status' => 'success',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,

                // legacy role
                'role' => $user->role,

                // new enterprise identity fields
                'domain' => $user->domain?->name,
                'system_role' => $user->systemRole?->name,
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout successful'
        ]);
    }
}