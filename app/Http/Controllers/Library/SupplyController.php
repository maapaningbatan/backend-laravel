<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Library\LibSupply;
use Illuminate\Support\Facades\Log;

class SupplyController extends Controller
{
    // ✅ List active supplies
    public function index()
    {
        try {
            $supplies = LibSupply::select('id', 'stock_no', 'supplies_desc', 'unit_id', 'category_id', 'unit_value', 'is_active')
                ->with([
                    'category:id,category_desc',
                    'unit:id,unit_code'
                ])
                ->where('is_archived', 0)
                ->orderBy('id')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $supplies
            ]);
        } catch (\Exception $e) {
            Log::error('Fetch Supplies Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch supplies.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ✅ List archived supplies
    public function archived()
    {
        $supplies = LibSupply::with(['category:id,category_desc', 'unit:id,unit_code'])
            ->where('is_archived', 1)
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $supplies
        ]);
    }

    // ✅ Show a single supply
    public function show($id)
    {
        $supply = LibSupply::with(['category', 'unit'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => $supply]);
    }

    // ✅ Create new supply
    public function store(Request $request)
    {
        $validated = $request->validate([
            'stock_no' => 'required|string|max:255|unique:lib_supplies,stock_no',
            'supplies_desc' => 'required|string|max:255',
            'category_id' => 'required|exists:lib_categories,id',
            'unit_id' => 'required|exists:lib_units,id',
            'unit_value' => 'required|numeric|min:0',
            'supplies_reorder_point' => 'nullable|integer|min:0',
        ]);

        $supply = LibSupply::create(array_merge($validated, ['is_archived' => 0]));

        return response()->json(['success' => true, 'data' => $supply], 201);
    }

    // ✅ Update supply
    public function update(Request $request, $id)
    {
        $supply = LibSupply::findOrFail($id);

        $validated = $request->validate([
            'stock_no' => 'required|string|max:255|unique:lib_supplies,stock_no,' . $id,
            'supplies_desc' => 'required|string|max:255',
            'category_id' => 'required|exists:lib_categories,id',
            'unit_id' => 'required|exists:lib_units,id',
            'unit_value' => 'required|numeric|min:0',
            'supplies_reorder_point' => 'nullable|integer|min:0',
        ]);

        $supply->update($validated);

        return response()->json(['success' => true, 'data' => $supply]);
    }

    // ✅ Archive supply
     public function archive($id)
    {
        $supply = LibSupply::findOrFail($id);
        $supply->update(['is_archived' => 1]);

        return response()->json(['message' => 'Archived successfully']);
    }

    // ✅ Restore supply
    public function restore($id)
    {
        $supply = LibSupply::findOrFail($id);
        $supply->update(['is_archived' => 0]);

        return response()->json(['success' => true, 'message' => 'Restored successfully.']);
    }

    // ✅ Permanently delete
    public function forceDelete($id)
    {
        $supply = LibSupply::findOrFail($id);
        $supply->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
    }
}
