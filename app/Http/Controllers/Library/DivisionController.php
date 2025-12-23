<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibDivision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DivisionController extends Controller
{
    /**
     * Display all active divisions (not archived)
     */
public function index()
{
    $divisions = LibDivision::where('is_archived', 0)
        ->with(['office', 'region'])
        ->orderBy('division_desc')
        ->select('id', 'division_code', 'division_desc', 'office_id', 'region_id')
        ->get();

    return response()->json($divisions);
}


    /**
     * Display all archived divisions
     */
    public function archived()
    {
        return response()->json(
            LibDivision::where('is_archived', 1)
                ->with(['office', 'region'])
                ->orderBy('division_desc')
                ->select('id', 'division_code', 'division_desc', 'office_id', 'region_id')
                ->get()
        );
    }

    /**
     * Get divisions by office
     */
    public function byOffice($officeId)
    {
        return response()->json(
            LibDivision::where('office_id', $officeId)
                ->where('is_archived', 0)
                ->orderBy('division_desc')
                ->select('id', 'division_code', 'division_desc', 'office_id', 'region_id')
                ->get()
        );
    }

    /**
     * Show a single division
     */
    public function show($id)
    {
        $division = LibDivision::with('office')->find($id);

        if (!$division) {
            return response()->json(['message' => 'Division not found'], 404);
        }

        $regionId = $division->region_id ?? $division->office?->region_id ?? null;

        return response()->json([
            'id' => $division->id,
            'division_code' => $division->division_code,
            'division_desc' => $division->division_desc,
            'office_id' => $division->office_id,
            'region_id' => $regionId,
        ]);
    }

    /**
     * Store new division
     */
    public function store(Request $request)
    {
        $request->validate([
            'division_code' => 'required|string|max:50',
            'division_desc' => 'required|string|max:255',
            'region_id'     => 'required|exists:lib_regions,id',
            'office_id'     => 'required|exists:lib_offices,id',
        ]);

        $division = LibDivision::create([
            'division_code' => $request->division_code,
            'division_desc' => $request->division_desc,
            'region_id'     => $request->region_id,
            'office_id'     => $request->office_id,
            'is_archived'   => 0,
            'created_by'    => Auth::id(),
            'updated_by'    => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Division created successfully',
            'division' => $division,
        ]);
    }

    /**
     * Update division
     */
    public function update(Request $request, $id)
    {
        $division = LibDivision::find($id);

        if (!$division) {
            return response()->json(['message' => 'Division not found'], 404);
        }

        $request->validate([
            'division_code' => 'required|string|max:50',
            'division_desc' => 'required|string|max:255',
            'region_id'     => 'required|exists:lib_regions,id',
            'office_id'     => 'required|exists:lib_offices,id',
        ]);

        $division->update([
            'division_code' => $request->division_code,
            'division_desc' => $request->division_desc,
            'region_id'     => $request->region_id,
            'office_id'     => $request->office_id,
            'updated_by'    => Auth::id(),
        ]);

        return response()->json(['message' => 'Division updated successfully', 'division' => $division]);
    }

    /**
     * Archive (set is_archived = 1)
     */
    public function archive($id)
    {
        $division = LibDivision::find($id);
        if (!$division) {
            return response()->json(['message' => 'Division not found'], 404);
        }

        $division->is_archived = 1;
        $division->save();

        return response()->json(['message' => 'Division archived successfully']);
    }

    /**
     * Restore (set is_archived = 0)
     */
    public function restore($id)
    {
        $division = LibDivision::find($id);
        if (!$division) {
            return response()->json(['message' => 'Division not found'], 404);
        }

        $division->is_archived = 0;
        $division->save();

        return response()->json(['message' => 'Division restored successfully']);
    }

    /**
     * Permanently delete
     */
    public function forceDelete($id)
    {
        $division = LibDivision::find($id);
        if (!$division) {
            return response()->json(['message' => 'Division not found'], 404);
        }

        $division->delete();

        return response()->json(['message' => 'Division permanently deleted']);
    }
}
