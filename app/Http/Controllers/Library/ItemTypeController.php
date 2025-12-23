<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibItemType;
use Illuminate\Http\JsonResponse;

class ItemTypeController extends Controller
{
    /**
     * Get all item types.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $itemTypes = LibItemType::select('id', 'itemtype_desc')
                ->orderBy('itemtype_desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $itemTypes
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch item types',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function show($id): JsonResponse
{
    try {
        // Use 'id' because that's the actual primary key in your table
        $itemType = LibItemType::select('id', 'itemtype_desc')
            ->where('id', $id)
            ->first();

        if (!$itemType) {
            return response()->json([
                'success' => false,
                'message' => 'Item type not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $itemType
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch item type',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
