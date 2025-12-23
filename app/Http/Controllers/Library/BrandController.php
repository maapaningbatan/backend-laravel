<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Library\LibBrand;
use Illuminate\Http\JsonResponse;

class BrandController extends Controller
{
    // List active brands
    public function index(): JsonResponse
{
    try {
        $brands = LibBrand::where('is_archived', 0)
            ->select('id', 'brand_desc')
            ->orderBy('brand_desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $brands
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch brands',
            'error' => $e->getMessage()
        ], 500);
    }
}


    // List archived brands
    public function archived()
    {
        $brands = LibBrand::where('is_archived', 1)->get();
        return response()->json($brands);
    }

    // Show single brand
    public function show($id)
    {
        $brand = LibBrand::findOrFail($id);
        return response()->json($brand);
    }

    // Create new brand
    public function store(Request $request)
    {
        $request->validate([
            'brand_desc' => 'required|string|max:255|unique:lib_brands,brand_desc',
        ]);

        $brand = LibBrand::create([
            'brand_desc' => $request->brand_desc,
            'is_archived' => 0,
        ]);

        return response()->json($brand, 201);
    }

    // Update brand
    public function update(Request $request, $id)
    {
        $brand = LibBrand::findOrFail($id);

        $request->validate([
            'brand_desc' => 'required|string|max:255|unique:lib_brands,brand_desc,' . $id,
        ]);

        $brand->update(['brand_desc' => $request->brand_desc]);

        return response()->json($brand);
    }

    // Archive brand
    public function archive($id)
    {
        $brand = LibBrand::findOrFail($id);
        $brand->update(['is_archived' => 1]);

        return response()->json(['message' => 'Archived successfully']);
    }

    // Restore brand
    public function restore($id)
    {
        $brand = LibBrand::findOrFail($id);
        $brand->update(['is_archived' => 0]);

        return response()->json(['message' => 'Restored successfully']);
    }

    // Permanent delete
    public function forceDelete($id)
    {
        $brand = LibBrand::findOrFail($id);
        $brand->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
