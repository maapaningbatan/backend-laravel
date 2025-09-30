<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Library\LibModule;
use App\Models\Library\LibPermission;

class ModuleController extends Controller
{
    // List all modules
    public function index()
    {
        return response()->json(LibModule::whereNull('deleted_at')->get());
    }

    // Store a new module
public function storeWithPermissions(Request $request)
{
    $request->validate([
        'module_name' => 'required|string|max:255',
        'table_name'  => 'required|string|max:255',
        'permissions' => 'required|array', // [{ userlevel_id, can_add, can_edit, can_view, can_delete }]
    ]);

    try {
        // 1️⃣ Create the module
        $module = LibModule::create([
            'module_name' => $request->module_name,
            'table_name'  => $request->table_name,
        ]);

        // 2️⃣ Assign permissions for each user level
        foreach ($request->permissions as $perm) {
            LibPermission::create([
                'userlevel_id' => $perm['userlevel_id'],
                'module_id'    => $module->id,
                'can_add'      => $perm['can_add'] ?? 0,
                'can_edit'     => $perm['can_edit'] ?? 0,
                'can_view'     => $perm['can_view'] ?? 0,
                'can_delete'   => $perm['can_delete'] ?? 0,
                'created_by'   => auth()->id(),
                'updated_by'   => auth()->id(),
            ]);
        }

        return response()->json([
            'id'      => $module->id,
            'module'  => $module,
            'message' => 'Module and permissions created successfully',
        ], 201);

    } catch (\Exception $e) {
        \Log::error('Failed to create module with permissions: '.$e->getMessage());
        return response()->json([
            'error'   => 'Failed to create module with permissions',
            'message' => $e->getMessage(),
        ], 500);
    }
}

}
