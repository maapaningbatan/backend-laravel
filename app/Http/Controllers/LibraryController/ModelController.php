<?php

namespace App\Http\Controllers\LibraryController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Library\LibModel;

class ModelController extends Controller
{
    public function index()
    {
        $models = LibModel::all(); // Must match the imported class
        return response()->json($models);
    }
}
