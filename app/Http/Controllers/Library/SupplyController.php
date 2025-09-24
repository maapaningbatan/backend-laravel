<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibSupply;

class SupplyController extends Controller
{
    public function index()
    {
        $supplies = LibSupply::with('category', 'unit')->get();

        return response()->json($supplies);
    }
}
