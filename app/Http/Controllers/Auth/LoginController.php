<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Tables\TblUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        $fields = $request->validated();

        // Find user by username or email
        $user = TblUser::where('Username', $fields['login'])
                       ->orWhere('Email_Address', $fields['login'])
                       ->first();

        if (!$user || !Hash::check($fields['password'], $user->Password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if ($user->Activated == 0) {
            return response()->json(['message' => 'Account is not activated yet.'], 403);
        }

        // Generate Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'user' => $user,
            'token' => $token,
        ], 200);
    }

   public function user(Request $request)
{
    $user = $request->user(); // Logged-in user via Sanctum

    if (!$user) {
        return response()->json(['message' => 'Not authenticated'], 401);
    }

    return response()->json([
        'Username'      => $user->Username,           // matches your DB column
        'Email_Address' => $user->Email_Address,     // matches your DB column
        'avatar'        => $user->avatar ?? null,    // optional, or null if not available
    ]);
}


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.'
        ]);
    }
}
