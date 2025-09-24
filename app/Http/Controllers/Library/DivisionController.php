<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibDivision;

class DivisionController extends Controller
{
    public function index()
    {
        return LibDivision::whereNull('deleted_at')
            ->orderBy('division_desc')
            ->select('id', 'division_desc', 'office_id')
            ->get();
    }

    public function byOffice($officeId)
    {
        return LibDivision::where('office_id', $officeId)
            ->whereNull('deleted_at')
            ->orderBy('division_desc')
            ->select('id', 'division_desc', 'office_id')
            ->get();
    }

    public function show($id)
    {
        $division = LibDivision::find($id);

        if (!$division) {
            return response()->json(['message' => 'Division not found'], 404);
        }

        return response()->json($division);
    }
}
