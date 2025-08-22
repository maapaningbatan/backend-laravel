<?php

namespace App\Http\Controllers\LibraryController;

use App\Http\Controllers\Controller;
use App\Models\Library\LibUserLevel;

class UserLevelController extends Controller
{
    // List all user levels (not deleted)
    public function index()
    {
        $userLevels = LibUserLevel::select('Userlevel_Id', 'Userlevel')
            ->whereNull('deleted_at')
            ->where('Userlevel', '<>', 'Administrator')  // Exclude Administration
            ->get();

        return response()->json($userLevels);
    }
}
