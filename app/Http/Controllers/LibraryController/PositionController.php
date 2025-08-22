<?php

namespace App\Http\Controllers\LibraryController;

use App\Http\Controllers\Controller;
use App\Models\Library\LibPosition;

class PositionController extends Controller
{
    public function index()
    {
        return LibPosition::orderBy('Position')
            ->select('Position_Id', 'Position')
            ->get(); // SoftDeletes automatically excludes "delete_at" rows
    }
}
