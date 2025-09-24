<?php

namespace App\Http\Controllers\Library;

use App\Models\Library\LibRegion;
use App\Http\Controllers\Controller;

class RegionController extends Controller
{
    /**
     * Display a listing of regions.
     */
    public function index()
    {
        // Fetch only non-deleted regions and order by region_name
        $regions = LibRegion::whereNull('deleted_at')
            ->orderBy('region_code')
            ->select('id', 'region_desc', 'region_code')
            ->get();

        return response()->json($regions);
    }

    /**
     * Display a specific region.
     */
    public function show($id)
    {
        $region = LibRegion::findOrFail($id);
        return response()->json($region);
    }
}
