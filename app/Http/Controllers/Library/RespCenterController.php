<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Library\LibRespCenter;

class RespCenterController extends Controller
{
    /**
     * List all responsibility centers with optional pagination and filtering.
     */
    public function index(Request $request)
    {
        $query = LibRespCenter::query();
        if ($request->has('is_archived')) {
            $query->where('is_archived', $request->is_archived);
        }

        $respCenters = $query->orderBy('resp_center_code')
                             ->paginate($request->get('per_page', 50));

        return response()->json($respCenters);
    }

    /**
     * Store a new responsibility center.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'resp_center_code'   => 'nullable|string|max:255',
            'resp_center_desc'   => 'nullable|string|max:255',
            'resp_center_office' => 'nullable|string|max:255',
            'created_by'         => 'required|integer',
            'updated_by'         => 'nullable|integer',
            'region_id'          => 'required|integer',
            'is_archived'        => 'nullable|boolean',
        ]);

        $data['date_created'] = now();
        $respCenter = LibRespCenter::create($data);

        return response()->json($respCenter, 201);
    }

    /**
     * Show a single responsibility center.
     */
    public function show(LibRespCenter $respCenter)
    {
        return response()->json($respCenter);
    }

    /**
     * Update an existing responsibility center.
     */
    public function update(Request $request, LibRespCenter $respCenter)
    {
        $data = $request->validate([
            'resp_center_code'   => 'nullable|string|max:255',
            'resp_center_desc'   => 'nullable|string|max:255',
            'resp_center_office' => 'nullable|string|max:255',
            'updated_by'         => 'required|integer',
            'region_id'          => 'nullable|integer',
            'is_archived'        => 'nullable|boolean',
        ]);

        $data['date_updated'] = now();
        $respCenter->update($data);

        return response()->json($respCenter);
    }

    /**
     * Delete a responsibility center.
     */
    public function destroy(LibRespCenter $respCenter)
    {
        $respCenter->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
