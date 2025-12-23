<?php

namespace App\Http\Controllers\Library;

use App\Models\Library\LibRegion;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    /**
     * Display a listing of active regions.
     */
    public function index()
    {
        $regions = LibRegion::where('is_archived', 0)
            ->orderBy('region_code')
            ->select('id', 'region_desc', 'region_code', 'location', 'zip_code')
            ->get();

        return response()->json($regions);
    }

    /**
     * Display a listing of archived regions.
     */
    public function archived()
    {
        $regions = LibRegion::where('is_archived', 1)
            ->orderBy('region_code')
            ->select('id', 'region_desc', 'region_code', 'location', 'zip_code')
            ->get();

        return response()->json($regions);
    }

    /**
     * Store a newly created region.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'region_code' => 'required|string|max:50|unique:lib_regions,region_code',
            'region_desc' => 'required|string|max:255',
            'location'    => 'nullable|string|max:255',
            'zip_code'    => 'nullable|string|max:20',
        ]);

        $validated['created_by'] = Auth::id();

        $region = LibRegion::create($validated);

        return response()->json([
            'message' => 'Region successfully created.',
            'region' => $region,
        ], 201);
    }

    /**
     * Display a single region.
     */
    public function show($id)
    {
        $region = LibRegion::findOrFail($id);
        return response()->json($region);
    }

    /**
     * Update a region.
     */
    public function update(Request $request, $id)
    {
        $region = LibRegion::findOrFail($id);

        $validated = $request->validate([
            'region_code' => 'required|string|max:50|unique:lib_regions,region_code,' . $region->id,
            'region_desc' => 'required|string|max:255',
            'location'    => 'nullable|string|max:255',
            'zip_code'    => 'nullable|string|max:20',
        ]);

        $validated['updated_by'] = Auth::id();

        $region->update($validated);

        return response()->json([
            'message' => 'Region updated successfully.',
            'data' => $region,
        ]);
    }

    /**
     * Archive a region (soft archive using is_archived flag).
     */
    public function archive($id)
    {
        $region = LibRegion::findOrFail($id);
        $region->update([
            'is_archived' => 1,
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['message' => 'Region archived successfully.']);
    }

    /**
     * Restore an archived region.
     */
    public function restore($id)
    {
        $region = LibRegion::findOrFail($id);
        $region->update([
            'is_archived' => 0,
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['message' => 'Region restored successfully.']);
    }

    /**
     * Permanently delete a region.
     */
    public function forceDelete($id)
    {
        $region = LibRegion::findOrFail($id);
        $region->delete();

        return response()->json(['message' => 'Region permanently deleted.']);
    }
}
