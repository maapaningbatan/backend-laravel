<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Library\LibBrand;

class BrandController extends Controller
{
    public function index()
    {
        $brands = LibBrand::select(
            'Brand_Id',
            'Brand_Description'
        )->get();

        return response()->json($brands);
    }
}
