<?php

namespace App\Http\Controllers\LibraryController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Library\LibCategory;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = LibCategory::all(); // Must match the imported class
        return response()->json($categories);
    }
}
