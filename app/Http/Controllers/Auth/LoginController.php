<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Models\Tables\TblUser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Library\LibUserLevel;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /**
     * Handle login request
     */
public function login(LoginRequest $request)
{
    try {
        $fields = $request->validated();

        // Find user by username or email
        $user = TblUser::where('username', $fields['login'])
                       ->orWhere('email', $fields['login'])
                       ->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if (!$user->activated) {
            return response()->json(['message' => 'Account not activated'], 403);
        }

        // Load user level with permissions + soft-deleted modules
$userLevel = LibUserLevel::with('permissions.module')->find($user->user_level_id);


        if (!$userLevel) {
            return response()->json([
                'message' => 'User level not found',
                'user_level_id' => $user->user_level_id
            ], 404);
        }

        // Map permissions keyed by lowercase module_name AND include module_id & table_name
        $permissions = ($userLevel->permissions ?? collect())
            ->filter(fn($perm) => $perm->module !== null) // skip null modules
            ->mapWithKeys(function ($perm) {
                $moduleName = strtolower($perm->module->module_name ?? 'unknown');
                return [$moduleName => [
                    'add'        => (bool)$perm->can_add,
                    'edit'       => (bool)$perm->can_edit,
                    'view'       => (bool)$perm->can_view,
                    'delete'     => (bool)$perm->can_delete,
                    'module_id'  => $perm->module->id,
                    'table_name' => $perm->module->table_name,
                    'deleted_at' => $perm->module->deleted_at,
                ]];
            })->toArray();

        // Create Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'user_level_id' => $userLevel->id,
                'user_level_name' => $userLevel->userlevel,
                'permissions' => $permissions,
            ],
            'token' => $token,
        ], 200);

    } catch (\Throwable $e) {
        \Log::error('Login failed: ' . $e->getMessage());
        return response()->json([
            'message' => 'Server error',
            'error' => $e->getMessage(),
        ], 500);
    }
}


  /**
     * Fetch logged-in user details
     */

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out successfully.']);
        } catch (\Throwable $e) {
            \Log::error('Logout failed: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
