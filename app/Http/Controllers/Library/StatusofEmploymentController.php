<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibStatusOfEmployment;
use Illuminate\Http\Request;

class StatusOfEmploymentController extends Controller
{
    public function index()
    {
        $data = LibStatusOfEmployment::where('is_archived', 0)->get();

        return response()->json($data);
    }

    public function show($id)
    {
        $soe = LibStatusOfEmployment::findOrFail($id);

        return response()->json($soe);
    }

    public function archived()
    {
        $data = LibStatusOfEmployment::where('is_archived', 1)->get();

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'soe' => 'required|string|max:255|unique:lib_soe,soe',
        ]);

        $soe = LibStatusOfEmployment::create([
            'soe' => $request->soe,
            'is_archived' => 0,
        ]);

        return response()->json($soe, 201);
    }

    public function update(Request $request, $id)
    {
        $soe = LibStatusOfEmployment::findOrFail($id);

        $request->validate([
            'soe' => 'required|string|max:255|unique:lib_soe,soe,'.$id,
        ]);

        $soe->update(['soe' => $request->soe]);

        return response()->json($soe);
    }

    public function archive($id)
    {
        $soe = LibStatusOfEmployment::findOrFail($id);
        $soe->update(['is_archived' => 1]);

        return response()->json(['message' => 'Archived successfully']);
    }

    public function restore($id)
    {
        $soe = LibStatusOfEmployment::findOrFail($id);
        $soe->update(['is_archived' => 0]);

        return response()->json(['message' => 'Restored successfully']);
    }

    public function forceDelete($id)
    {
        $soe = LibStatusOfEmployment::findOrFail($id);
        $soe->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
