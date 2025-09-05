<?php

namespace App\Http\Controllers\LibraryController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Library\LibUnit;

class UnitController extends Controller
{
    public function index()
    {
        $units = LibUnit::all(); // Must match the imported class
        return response()->json($units);
    }
}
