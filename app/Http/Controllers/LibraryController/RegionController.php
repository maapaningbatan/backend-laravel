<?php

namespace App\Http\Controllers\LibraryController;

use App\Models\Library\LibRegion;
use App\Http\Controllers\Controller;

class RegionController extends Controller
{
    public function index()
    {
        // This will automatically exclude soft deleted rows
        return LibRegion::orderBy('Region')
            ->select('Region_Id', 'Region')
            ->get();
    }

    public function show($id)
{
    $region = LibRegion::findOrFail($id);
    return response()->json($region);
}
}
