<?php

namespace App\Http\Controllers\LibraryController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Library\LibSupply;

class SupplyController extends Controller
{
    public function index()
    {
        $supplies = LibSupply::all(); // Must match the imported class
        return response()->json($supplies);
    }
}
