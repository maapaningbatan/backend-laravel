<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PositionController extends Controller
{
    /**
     * Display a listing of positions.
     */
    public function index()
    {
        return response()->json(
            LibPosition::select('id', 'position_code', 'position_desc')
                ->whereNull('deleted_at')
                ->orderBy('position_code')
                ->get()
        );
    }

    /**
     * Store a newly created position in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'position_code' => 'required|string|max:255',
            'position_desc' => 'nullable|string|max:255',
        ]);

        $position = LibPosition::create([
            'position_code' => $request->position_code,
            'position_desc' => $request->position_desc,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return response()->json($position, 201);
    }

    /**
     * Display the specified position.
     */
    public function show($id)
    {
        $position = LibPosition::findOrFail($id);

        return response()->json($position);
    }

    /**
     * Update the specified position in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'position_code' => 'required|string|max:255',
            'position_desc' => 'nullable|string|max:500',
        ]);

        $position = LibPosition::findOrFail($id);

        $position->update([
            'position_code' => $validated['position_code'],
            'position_desc' => $validated['position_desc'] ?? null,
            'updated_by' => Auth::id(),
        ]);

        return response()->json($position);
    }

    /**
     * Soft delete the specified position.
     */
    public function destroy($id)
    {
        $position = LibPosition::findOrFail($id);
        $position->deleted_by = Auth::id();
        $position->save();
        $position->delete();

        return response()->json(['message' => 'Position deleted successfully']);
    }
}
