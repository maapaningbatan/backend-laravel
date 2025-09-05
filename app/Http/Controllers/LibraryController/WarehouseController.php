<?php

namespace App\Http\Controllers\LibraryController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Library\LibWarehouse;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = LibWarehouse::all(); // Must match the imported class
        return response()->json($warehouses);
    }
}
