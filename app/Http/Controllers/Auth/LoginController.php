<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Models\Tables\TblUser;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        $fields = $request->validated();

        // Find user by username or email
        $user = TblUser::where('username', $fields['login'])
                       ->orWhere('email', $fields['login'])
                       ->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if ($user->activated == 0) {
            return response()->json(['message' => 'Account is not activated yet.'], 403);
        }

        // Generate Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'user'    => $user,
            'token'   => $token,
        ], 200);
    }

    public function user(Request $request)
    {
        try {
            $user = $request->user(); // Current logged-in user via Sanctum

            if (!$user) {
                return response()->json(['message' => 'Not authenticated'], 401);
            }

            // Join tbl_users with lib_employees (history table)
            $employee = DB::table('tbl_users as u')
                ->leftJoin('lib_employees as e', 'u.employee_id', '=', 'e.id')
                ->select(
                    'u.id as user_id',
                    'u.username',
                    'u.email',
                    'u.activated',
                    'e.first_name',
                    'e.middle_name',
                    'e.last_name',
                    'e.sex',
                    'e.position_id',
                    'e.office_id',
                    'e.division_id',
                    'e.cluster_id'
                )
                ->where('u.id', $user->id)
                ->first();

            if (!$employee) {
                return response()->json(['message' => 'Employee record not found'], 404);
            }

            return response()->json($employee);
        } catch (\Exception $e) {
            \Log::error('User fetch failed: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.'
        ]);
    }
}
