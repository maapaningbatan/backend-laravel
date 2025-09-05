<?php

namespace App\Http\Controllers\LibraryController;

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
            LibPosition::orderBy('Position')
                ->select('Position_Id', 'Position', 'Position_Desc')
                ->get()
        );
    }

    /**
     * Store a newly created position in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'Position' => 'required|string|max:255',
            'Position_Desc' => 'nullable|string|max:255',
        ]);

        $position = LibPosition::create([
            'Position' => $request->Position,
            'Position_Desc' => $request->Position_Desc,
            'created_by' => Auth::id(), // <-- required!
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
            'Position' => 'required|string|max:255',
            'Position_Desc' => 'nullable|string|max:500',
        ]);

        $position = LibPosition::findOrFail($id);

        $position->update([
            'Position' => $validated['Position'],
            'Position_Desc' => $validated['Position_Desc'] ?? null,
            'updated_by' => auth()->id() ?? null,
        ]);

        return response()->json($position);
    }

    /**
     * Soft delete the specified position.
     */
    public function destroy($id)
    {
        $position = LibPosition::findOrFail($id);
        $position->deleted_by = auth()->id() ?? null;
        $position->save();
        $position->delete();

        return response()->json(['message' => 'Position deleted successfully']);
    }
}
