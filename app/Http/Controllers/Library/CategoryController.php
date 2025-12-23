<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Library\LibCategory;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    // List active categories
   public function index(): JsonResponse
{
    try {
        $categories = LibCategory::where('is_archived', 0)
            ->select('id', 'category_desc', 'category_code')
            ->orderBy('category_desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch categories',
            'error' => $e->getMessage()
        ], 500);
    }
}

    // List archived categories
    public function archived()
    {
        $categories = LibCategory::where('is_archived', 1)->get();
        return response()->json($categories);
    }

    // Show single category
    public function show($id)
    {
        $category = LibCategory::findOrFail($id);
        return response()->json($category);
    }

    // Create new category
    public function store(Request $request)
    {
        $request->validate([
            'category_desc' => 'required|string|max:255|unique:lib_categories,category_desc',
            'category_code' => 'required|string|max:100|unique:lib_categories,category_code',
        ]);

        $category = LibCategory::create([
            'category_desc' => $request->category_desc,
            'category_code' => $request->category_code,
            'is_archived' => 0,
        ]);

        return response()->json($category, 201);
    }

    // Update category
    public function update(Request $request, $id)
    {
        $category = LibCategory::findOrFail($id);

        $request->validate([
            'category_desc' => 'required|string|max:255|unique:lib_categories,category_desc,' . $id,
            'category_code' => 'required|string|max:100|unique:lib_categories,category_code,' . $id,
        ]);

        $category->update([
            'category_desc' => $request->category_desc,
            'category_code' => $request->category_code,
        ]);

        return response()->json($category);
    }

    // Archive category
    public function archive($id)
    {
        $category = LibCategory::findOrFail($id);
        $category->update(['is_archived' => 1]);

        return response()->json(['message' => 'Archived successfully']);
    }

    // Restore category
    public function restore($id)
    {
        $category = LibCategory::findOrFail($id);
        $category->update(['is_archived' => 0]);

        return response()->json(['message' => 'Restored successfully']);
    }

    // Permanent delete
    public function forceDelete($id)
    {
        $category = LibCategory::findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
