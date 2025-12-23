<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibEmployee;
use App\Models\Library\LibModule;
use App\Models\Library\LibPermission;
use App\Models\Tables\TblUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * List all users with their positions, offices, and related info.
     */
    public function index()
    {
        $users = TblUser::with(['region', 'position', 'office', 'division', 'center', 'userlevel'])
            ->select(
                'id',
                'username',
                'email',
                'first_name',
                'middle_name',
                'last_name',
                'position_id',
                'office_id',
                'division_id',
                'region_id',
                'cluster_id',
                'activated',
                'user_level_id',
                'is_archived'
            )
            ->where('is_archived', 0) // <-- only get non-archived users
            ->get();

        return response()->json($users);
    }

    /**
     * Store a new user.
     */
    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'username' => 'required|string|unique:tbl_users,username',
            'email' => 'required|email|unique:tbl_users,email',
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'suffix' => 'nullable|string',
            'position_id' => 'required|integer|exists:lib_positions,id',
            'office_id' => 'required|integer|exists:lib_offices,id',
            'division_id' => 'nullable|integer|exists:lib_divisions,id',
            'region_id' => 'required|integer|exists:lib_regions,id',
            'cluster_id' => 'nullable|integer',
            'user_level_id' => 'required|integer|exists:lib_user_levels,id',
            'password' => 'required|string|min:6|confirmed', // expects password_confirmation field
        ]);

        // Hash the password
        $validated['password'] = Hash::make($validated['password']);

        // Create the user
        $user = TblUser::create($validated);

        // Return success response with newly created user
        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
        ], 201);
    }

    public function show($id)
    {
        $user = TblUser::with(['region', 'position', 'office', 'division', 'center'])
            ->findOrFail($id);

        return response()->json($user);
    }

    /**
     * Update a user.
     */
    public function update(Request $request, $id)
    {
        $user = TblUser::findOrFail($id);

        // Validate incoming request
        $data = $request->only([
            'username',
            'email',
            'first_name',
            'middle_name',
            'last_name',
            'sex',
            'position_id',
            'office_id',
            'division_id',
            'region_id',
            'cluster_id',
            'user_level_id',
            'password', // <-- add password here
        ]);

        // Hash password if provided
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']); // remove if empty so it doesn’t overwrite
        }

        $user->update($data);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user,
        ]);
    }

    /**
     * Get authenticated user's profile.
     */
    public function user(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Not authenticated'], 401);
        }

        $userData = TblUser::with(['region', 'office', 'division'])
            ->select(
                'id as User_Id',
                'username',
                'email',
                'activated',
                'user_level_id',
                'first_name',
                'middle_name',
                'last_name',
                'suffix',
                'position_id',
                'region_id',
                'office_id',
                'division_id',
                'cluster_id',
                'center_id'
            )
            ->where('id', $user->id)
            ->first();

        if (! $userData) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Add desc fields
        $userData->region_desc = $userData->region->region_desc ?? null;
        $userData->office_desc = $userData->office->office_desc ?? null;
        $userData->division_desc = $userData->division->division_desc ?? null;
        $userData->userlevel = $userData->userlevel->userlevel ?? null;

        // Fetch modules & permissions as before
        $modules = LibModule::whereNull('deleted_at')->get();
        $permissions = LibPermission::where('userlevel_id', $userData->user_level_id)->get();

        $formattedPermissions = [];
        foreach ($modules as $module) {
            $perm = $permissions->firstWhere('module_id', $module->id);
            $formattedPermissions[$module->table_name ?? $module->module_name] = [
                'add' => isset($perm) ? (bool) $perm->can_add : false,
                'edit' => isset($perm) ? (bool) $perm->can_edit : false,
                'view' => isset($perm) ? (bool) $perm->can_view : false,
                'delete' => isset($perm) ? (bool) $perm->can_delete : false,
            ];
        }

        return response()->json([
            'user' => $userData,
            'permissions' => $formattedPermissions,
        ]);
    }

    /**
     * Get users by division.
     */
    public function getByDivision(Request $request)
    {
        $divisionId = $request->query('division_id');

        if (! $divisionId) {
            return response()->json(['error' => 'division_id is required'], 400);
        }

        $employees = LibEmployee::where('Division', $divisionId)->get();

        return response()->json($employees);
    }

    /**
     * Toggle a user's activation status.
     */
    public function toggleActivation($id)
    {
        $user = TblUser::find($id);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->activated = ! $user->activated;
        $user->save();

        return response()->json([
            'id' => $user->id,
            'activated' => (bool) $user->activated,
        ]);
    }

    // Archive a single user
    public function archive($id)
    {
        $user = TblUser::find($id); // ✅ fetch the user by ID
        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->is_archived = 1;
        $user->save();

        return response()->json(['message' => 'User archived successfully']);
    }

    // Restore a single user
    public function restore($id)
    {
        $user = TblUser::find($id);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->is_archived = 0;
        $user->save();

        return response()->json(['message' => 'User restored successfully']);
    }

    // Permanently delete a user
    public function destroy($id)
    {
        $user = TblUser::find($id);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->forceDelete(); // Permanent delete

        return response()->json(['message' => 'User permanently deleted']);
    }

    // Get all archived users
    public function archived()
    {
        $archivedUsers = TblUser::with(['region', 'position', 'office', 'division', 'center', 'userlevel'])
            ->where('is_archived', 1)
            ->get();

        return response()->json($archivedUsers);
    }
}
