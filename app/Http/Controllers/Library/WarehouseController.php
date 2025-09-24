<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Library\LibWarehouse;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = LibWarehouse::select(
            'Warehouse_ID',
            'Warehouse_Code',
            'Warehouse_Description',
        )->get();
        return response()->json($warehouses);
    }
}
