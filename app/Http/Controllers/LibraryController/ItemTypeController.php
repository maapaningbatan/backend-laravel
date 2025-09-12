<?php

namespace App\Http\Controllers\LibraryController;

use App\Http\Controllers\Controller;
use App\Models\Library\LibItemType;

class ItemTypeController extends Controller
{
    public function index()
    {
        return response()->json(LibItemType::all(['itemtype_id', 'itemtype_name']));
    }
}
