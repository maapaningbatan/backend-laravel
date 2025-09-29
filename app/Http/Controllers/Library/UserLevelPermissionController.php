<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Library\LibPermission;
use App\Models\Library\LibModule;

class UserLevelPermissionController extends Controller
{
    // Fetch permissions for a given user level
    public function index($userlevelId)
    {
        $modules = LibModule::whereNull('deleted_at')->get();
        $permissions = LibPermission::where('userlevel_id', $userlevelId)
            ->get()
            ->keyBy('module_id');

        $result = [];

        foreach ($modules as $module) {
            $perm = $permissions->get($module->id);
            $result[$module->id] = [
                'add' => $perm->can_add ?? false,
                'edit' => $perm->can_edit ?? false,
                'view' => $perm->can_view ?? false,
                'delete' => $perm->can_delete ?? false,
            ];
        }

        return response()->json($result);
    }

    // Save/update permissions for a user level
    public function update(Request $request, $userlevelId)
    {
        $data = $request->permissions; // expects { moduleId: { add, edit, view, delete }, ... }

        foreach ($data as $moduleId => $perm) {
            LibPermission::updateOrCreate(
                ['userlevel_id' => $userlevelId, 'module_id' => $moduleId],
                [
                    'can_add' => $perm['add'] ? 1 : 0,
                    'can_edit' => $perm['edit'] ? 1 : 0,
                    'can_view' => $perm['view'] ? 1 : 0,
                    'can_delete' => $perm['delete'] ? 1 : 0,
                ]
            );
        }

        return response()->json(['message' => 'Permissions saved']);
    }
}
