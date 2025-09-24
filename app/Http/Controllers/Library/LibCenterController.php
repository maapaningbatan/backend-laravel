<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LibCenterController extends Controller
{
    // List all centers
    public function index()
    {
        return response()->json(LibCenter::whereNull('datedeleted')->get(), 200);
    }

    // Store new center
    public function store(Request $request)
    {
        $validated = $request->validate([
            'center' => 'required|string|max:50|unique:lib_center,center',
            'center_desc' => 'required|string|max:255',
        ]);

        $center = LibCenter::create([
            'center' => $validated['center'],
            'center_desc' => $validated['center_desc'],
            'createdby' => Auth::id() ?? 0,
            'datecreated' => now(),
        ]);

        return response()->json(['message' => 'Center created successfully', 'data' => $center], 201);
    }

    // Update center
    public function update(Request $request, $id)
    {
        $center = LibCenter::findOrFail($id);

        $validated = $request->validate([
            'center' => 'required|string|max:50|unique:lib_center,center,' . $id . ',center_id',
            'center_desc' => 'required|string|max:255',
        ]);

        $center->update([
            'center' => $validated['center'],
            'center_desc' => $validated['center_desc'],
            'updatedby' => Auth::id() ?? 0,
            'dateupdated' => now(),
        ]);

        return response()->json(['message' => 'Center updated successfully', 'data' => $center], 200);
    }

    // Soft delete
    public function destroy($id)
    {
        $center = LibCenter::findOrFail($id);

        $center->update([
            'deletedby' => Auth::id() ?? 0,
            'datedeleted' => now(),
        ]);

        return response()->json(['message' => 'Center deleted successfully'], 200);
    }
}
