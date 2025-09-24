<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Library\LibUnit;

class UnitController extends Controller
{
    public function index()
    {
        $units = LibUnit::select(
            'Unit_Id',
            'Unit_Type',
            'Unit_Description'
        )->get();
        return response()->json($units);
    }
}
