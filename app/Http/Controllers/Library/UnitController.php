<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Library\LibUnit;
use Illuminate\Http\JsonResponse;

class UnitController extends Controller
{
    // List active units
  public function index(): JsonResponse
{
    try {
        $units = LibUnit::where('is_archived', 0)
            ->select('id', 'unit_code', 'unit_desc')
            ->orderBy('unit_code')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $units
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch units',
            'error' => $e->getMessage()
        ], 500);
    }
}


    // List archived units
    public function archived()
    {
        $units = LibUnit::where('is_archived', 1)->get();
        return response()->json($units);
    }

    // Show single unit
    public function show($id)
    {
        $unit = LibUnit::findOrFail($id);
        return response()->json($unit);
    }

    // Create new unit
    public function store(Request $request)
    {
        $request->validate([
            'unit_code' => 'required|string|max:50|unique:lib_units,unit_code',
            'unit_desc' => 'required|string|max:255|unique:lib_units,unit_desc',
        ]);

        $unit = LibUnit::create([
            'unit_code' => $request->unit_code,
            'unit_desc' => $request->unit_desc,
            'is_archived' => 0,
        ]);

        return response()->json($unit, 201);
    }

    // Update unit
    public function update(Request $request, $id)
    {
        $unit = LibUnit::findOrFail($id);

        $request->validate([
            'unit_code' => 'required|string|max:50|unique:lib_units,unit_code,' . $id,
            'unit_desc' => 'required|string|max:255|unique:lib_units,unit_desc,' . $id,
        ]);

        $unit->update([
            'unit_code' => $request->unit_code,
            'unit_desc' => $request->unit_desc,
        ]);

        return response()->json($unit);
    }

    // Archive unit
    public function archive($id)
    {
        $unit = LibUnit::findOrFail($id);
        $unit->update(['is_archived' => 1]);

        return response()->json(['message' => 'Archived successfully']);
    }

    // Restore unit
    public function restore($id)
    {
        $unit = LibUnit::findOrFail($id);
        $unit->update(['is_archived' => 0]);

        return response()->json(['message' => 'Restored successfully']);
    }

    // Permanent delete
    public function forceDelete($id)
    {
        $unit = LibUnit::findOrFail($id);
        $unit->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
