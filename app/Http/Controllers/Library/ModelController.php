<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Library\LibModel;
use Illuminate\Http\JsonResponse;

class ModelController extends Controller
{
    // List active models
    public function index(): JsonResponse
{
    try {
        $models = LibModel::where('is_archived', 0)
            ->select('id', 'model_desc')
            ->orderBy('model_desc', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $models
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch models',
            'error' => $e->getMessage()
        ], 500);
    }
}


    // List archived models
    public function archived()
    {
        $models = LibModel::where('is_archived', 1)->get();
        return response()->json($models);
    }

    // Show single model
    public function show($id)
    {
        $model = LibModel::findOrFail($id);
        return response()->json($model);
    }

    // Create new model
    public function store(Request $request)
    {
        $request->validate([
            'model_desc' => 'required|string|max:255|unique:lib_models,model_desc',
        ]);

        $model = LibModel::create([
            'model_desc' => $request->model_desc,
            'is_archived' => 0,
        ]);

        return response()->json($model, 201);
    }

    // Update model
    public function update(Request $request, $id)
    {
        $model = LibModel::findOrFail($id);

        $request->validate([
            'model_desc' => 'required|string|max:255|unique:lib_models,model_desc,' . $id,
        ]);

        $model->update(['model_desc' => $request->model_desc]);

        return response()->json($model);
    }

    // Archive model
    public function archive($id)
    {
        $model = LibModel::findOrFail($id);
        $model->update(['is_archived' => 1]);

        return response()->json(['message' => 'Archived successfully']);
    }

    // Restore model
    public function restore($id)
    {
        $model = LibModel::findOrFail($id);
        $model->update(['is_archived' => 0]);

        return response()->json(['message' => 'Restored successfully']);
    }

    // Permanent delete
    public function forceDelete($id)
    {
        $model = LibModel::findOrFail($id);
        $model->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
