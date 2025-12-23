<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Library\LibWarehouse;

class WarehouseController extends Controller
{
    // List active warehouses
    public function index()
    {
        $warehouses = LibWarehouse::where('is_archived', 0)->get();
        return response()->json($warehouses);
    }

    // List archived warehouses
    public function archived()
    {
        $warehouses = LibWarehouse::where('is_archived', 1)->get();
        return response()->json($warehouses);
    }

    // Show single warehouse
    public function show($id)
    {
        $warehouse = LibWarehouse::findOrFail($id);
        return response()->json($warehouse);
    }

    // Create new warehouse
    public function store(Request $request)
    {
        $request->validate([
            'warehouse_code' => 'required|string|max:100|unique:lib_warehouses,warehouse_code',
            'warehouse_desc' => 'required|string|max:255',
        ]);

        $warehouse = LibWarehouse::create([
            'warehouse_code' => $request->warehouse_code,
            'warehouse_desc' => $request->warehouse_desc,
            'is_archived' => 0,
        ]);

        return response()->json($warehouse, 201);
    }

    // Update warehouse
    public function update(Request $request, $id)
    {
        $warehouse = LibWarehouse::findOrFail($id);

        $request->validate([
            'warehouse_code' => 'required|string|max:100|unique:lib_warehouses,warehouse_code,' . $id,
            'warehouse_desc' => 'required|string|max:255',
        ]);

        $warehouse->update([
            'warehouse_code' => $request->warehouse_code,
            'warehouse_desc' => $request->warehouse_desc,
        ]);

        return response()->json($warehouse);
    }

    // Archive warehouse
    public function archive($id)
    {
        $warehouse = LibWarehouse::findOrFail($id);
        $warehouse->update(['is_archived' => 1]);

        return response()->json(['message' => 'Archived successfully']);
    }

    // Restore warehouse
    public function restore($id)
    {
        $warehouse = LibWarehouse::findOrFail($id);
        $warehouse->update(['is_archived' => 0]);

        return response()->json(['message' => 'Restored successfully']);
    }

    // Permanent delete
    public function forceDelete($id)
    {
        $warehouse = LibWarehouse::findOrFail($id);
        $warehouse->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
