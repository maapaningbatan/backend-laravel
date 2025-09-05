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
    try {
        $user = $request->user(); // Logged-in user via Sanctum

        if (!$user) {
            return response()->json(['message' => 'Not authenticated'], 401);
        }

        // Join tbl_user with lib_employee
        $employee = \DB::table('tbl_user as u')
            ->leftJoin('lib_employee as e', 'u.employee_pk', '=', 'e.Employee_PK')
            ->select(
                'u.User_Id',
                'u.Username',
                'u.Email_Address',
                'u.Activated',
                'e.First_Name',
                'e.Middle_Name',
                'e.Last_Name',
                'e.Sex',
                'e.Position',
                'e.Office',
                'e.Division',
                'e.Cluster'
            )
            ->where('u.User_Id', $user->User_Id) // 👈 correct PK for tbl_user
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
