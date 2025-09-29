<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibUserLevel;

class UserLevelController extends Controller
{
    // List all user levels
public function index($userlevelId)
{
    $permissions = LibPermission::where('userlevel_id', $userlevelId)->get();
    return response()->json($permissions->values());
}

    // Show a single user level
    public function show($id)
    {
        $level = LibUserLevel::find($id);

        if (!$level) {
            return response()->json(['message' => 'User level not found'], 404);
        }

        return response()->json($level);
    }
}
