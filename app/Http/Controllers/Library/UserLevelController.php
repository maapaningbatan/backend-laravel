<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibUserLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserLevelController extends Controller
{
    // List all user levels
    public function index()
    {
        $userLevels = LibUserLevel::whereNull('deleted_at')
            ->orderBy('id', 'asc')
            ->get();

        return response()->json($userLevels);
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
public function store(Request $request)
{
    $request->validate([
        'userlevel' => 'required|string|max:255',
    ]);

    // Only create the user level, do NOT attach any modules
    $level = LibUserLevel::create([
        'userlevel'  => $request->userlevel,
        'created_by' => Auth::id(),
    ]);

    return response()->json($level, 201);
}

public function permissionsByLevel($id)
{
    $level = LibUserLevel::with('permissions.module')->findOrFail($id);

    // Only return assigned modules
    return response()->json($level->permissions);
}

public function update(Request $request, $id)
{
    $level = LibUserLevel::findOrFail($id);
    $level->update([
        'userlevel' => $request->userlevel,
        'updated_by' => auth()->id(),
    ]);

    return response()->json($level);
}

public function destroy($id)
{
    $level = LibUserLevel::findOrFail($id);
    $level->deleted_by = auth()->id();
    $level->save();

    $level->delete(); // soft delete

    return response()->json(['message' => 'Deleted']);
}
}

