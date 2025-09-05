<?php

namespace App\Http\Controllers\LibraryController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Library\LibSupplier;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = LibSupplier::all(); // Must match the imported class
        return response()->json($suppliers);
    }
}
