<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibCategory;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = LibCategory::select(
            'category_id',
            'category_desc',
            'category_code'
        )->get();

        return response()->json($categories);
    }
}
