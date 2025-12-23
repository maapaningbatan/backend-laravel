<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Library\LibModule;
use App\Models\Library\LibPermission;

class ModuleController extends Controller
{
    // List all modules (excluding deleted)
public function index(Request $request)
{
    $query = LibModule::query();

    if ($request->boolean('withDeleted')) {
        $query->withTrashed();
    } else {
        $query->whereNull('deleted_at');
    }

    $modules = $query->get();

    return response()->json($modules);
}

    // Store a new module with permissions
    public function storeWithPermissions(Request $request)
{
    $request->validate([
        'module_name' => 'required|string|unique:lib_modules,module_name',
        'table_name' => 'required|string|unique:lib_modules,table_name',
        'permissions' => 'required|array',
        'permissions.*.userlevel_id' => 'required|exists:lib_user_levels,id',
        'permissions.*.can_add' => 'boolean',
        'permissions.*.can_edit' => 'boolean',
        'permissions.*.can_view' => 'boolean',
        'permissions.*.can_delete' => 'boolean',
    ]);

    $module = LibModule::create([
        'module_name' => $request->module_name,
        'table_name' => $request->table_name,
        'created_by' => $request->created_by,
    ]);

    foreach ($request->permissions as $perm) {
        LibPermission::create([
            'userlevel_id' => $perm['userlevel_id'],
            'module_id' => $module->id,
            'can_add' => $perm['can_add'],
            'can_edit' => $perm['can_edit'],
            'can_view' => $perm['can_view'],
            'can_delete' => $perm['can_delete'],
            'created_by' => $request->created_by,
        ]);
    }

    return response()->json([
        'message' => 'Module added successfully with permissions',
        'module' => $module,
    ]);
}


    // Soft delete a module
    public function destroy($id)
    {
        try {
            $module = LibModule::findOrFail($id);

            // Soft delete module
            $module->deleted_by = auth()->id();
            $module->save();
            $module->delete();

            // Soft delete related permissions
            LibPermission::where('module_id', $id)
                ->update([
                    'deleted_by' => auth()->id(),
                    'deleted_at' => now(),
                ]);

            return response()->json([
                'message' => 'Module deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Failed to delete module: '.$e->getMessage());
            return response()->json([
                'error'   => 'Failed to delete module',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // Restore a soft-deleted module
public function restore($id)
{
    try {
        // Include deleted modules in the query
        $module = LibModule::withTrashed()->findOrFail($id);

        // Check if module is actually soft-deleted
        if (!$module->trashed()) {
            return response()->json([
                'message' => 'Module is not deleted or already active.',
            ], 400);
        }

        // ✅ Restore module
        $module->restore();
        $module->deleted_by = null;
        $module->save();

        // ✅ Restore related permissions
        LibPermission::where('module_id', $id)->update([
            'deleted_at' => null,
            'deleted_by' => null,
        ]);

        return response()->json([
            'message' => 'Module restored successfully',
            'module'  => $module,
        ], 200);

    } catch (\Exception $e) {
        \Log::error('Failed to restore module: ' . $e->getMessage());

        return response()->json([
            'error'   => 'Failed to restore module',
            'message' => $e->getMessage(),
        ], 500);
    }
}
}
