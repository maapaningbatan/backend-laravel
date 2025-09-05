<?php

namespace App\Http\Controllers\LibraryController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Library\LibBrand;

class BrandController extends Controller
{
    public function index()
    {
        $brands = LibBrand::all(); // Must match the imported class
        return response()->json($brands);
    }
}
