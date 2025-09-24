<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Library\LibModel;

class ModelController extends Controller
{
    public function index()
    {
        $models = LibModel::select(
            'model_id',
            'model_desc',
        )->get();
        return response()->json($models);
    }
}
