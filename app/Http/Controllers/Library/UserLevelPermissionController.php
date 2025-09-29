<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Library\LibPermission;

class UserLevelPermissionController extends Controller
{
    // Fetch permissions for a given user level
    public function index($userlevelId)
    {
        $modules = \App\Models\Library\LibModule::whereNull('deleted_at')->get();
        $permissions = LibPermission::where('userlevel_id', $userlevelId)->get();

        $result = $modules->map(function ($module) use ($permissions) {
            $perm = $permissions->firstWhere('module_id', $module->id);
            return [
                'id' => $perm->id ?? null,
                'module_id' => $module->id,
                'module_name' => $module->module_name,
                'table_name' => $module->table_name,
                'can_add' => $perm->can_add ?? 0,
                'can_edit' => $perm->can_edit ?? 0,
                'can_view' => $perm->can_view ?? 0,
                'can_delete' => $perm->can_delete ?? 0,
            ];
        });

        return response()->json($result);
    }

    // Show user level info (optional)
    public function show($userlevelId)
    {
        $level = \App\Models\Library\LibUserLevel::find($userlevelId);
        if (!$level) {
            return response()->json(['error' => 'User level not found'], 404);
        }
        return response()->json($level);
    }

    // Save/update permissions
    public function update(Request $request, $userlevelId)
    {
        $authUserId = auth()->id() ?? 0; // fallback if no auth

        if (!$request->has('permissions') || !is_array($request->permissions)) {
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        try {
            foreach ($request->permissions as $permData) {
                // Use ID if exists, otherwise create new
                $permission = $permData['id']
                    ? LibPermission::find($permData['id'])
                    : new LibPermission();

                $permission->userlevel_id = $userlevelId;
                $permission->module_id = $permData['module_id'];
                $permission->can_add = $permData['can_add'] ? 1 : 0;
                $permission->can_edit = $permData['can_edit'] ? 1 : 0;
                $permission->can_view = $permData['can_view'] ? 1 : 0;
                $permission->can_delete = $permData['can_delete'] ? 1 : 0;

                if (!$permission->exists) {
                    $permission->created_by = $authUserId;
                }
                $permission->updated_by = $authUserId;

                $permission->save();
            }

            return response()->json(['message' => 'Permissions saved successfully']);
        } catch (\Exception $e) {
            \Log::error("Failed to update permissions for userlevel {$userlevelId}: ".$e->getMessage());
            return response()->json([
                'error' => 'Failed to save permissions',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
