<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibOffice;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OfficeController extends Controller
{
    public function index()
    {
        return LibOffice::with('region')
            ->where('is_archived', false)
            ->orderBy('office_desc')
            ->get()
            ->map(function ($office) {
                return [
                    'id' => $office->id,
                    'office_code' => $office->office_code,
                    'office_desc' => $office->office_desc,
                    'region_desc' => $office->region?->region_desc ?? '',
                ];
            });
    }

    public function show($id)
    {
        $office = LibOffice::with('region')->findOrFail($id);

        return response()->json([
            'id' => $office->id,
            'office_code' => $office->office_code,
            'office_desc' => $office->office_desc,
            'region_id' => $office->region_id,
            'region_desc' => $office->region?->region_desc ?? '',
        ]);
    }

    public function getByCluster($clusterId)
    {
        return LibOffice::where('cluster_id', $clusterId)
            ->orderBy('office_desc')
            ->get(['id', 'office_code', 'office_desc', 'obs_head', 'region_id', 'cluster_id', 'bldg_id']);
    }

    public function getByRegion($regionId)
    {
        return LibOffice::where('region_id', $regionId)
            ->orderBy('office_desc')
            ->get(['id', 'office_code', 'office_desc', 'obs_head', 'region_id', 'cluster_id', 'bldg_id']);
    }

    public function update(Request $request, $id)
    {
        $office = LibOffice::findOrFail($id);

        $data = $request->only(['office_code', 'office_desc', 'region_id']);
        $office->update($data);

        return response()->json([
            'id' => $office->id,
            'office_code' => $office->office_code,
            'office_desc' => $office->office_desc,
            'region_desc' => $office->region?->region_desc ?? '',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'office_code' => ['required', 'string', 'max:50', Rule::unique('lib_offices', 'office_code')],
            'office_desc' => ['required', 'string', 'max:255'],
            'region_id'   => ['required', 'integer'],
        ]);

        $office = LibOffice::create([
            'office_code' => $validated['office_code'],
            'office_desc' => $validated['office_desc'],
            'region_id'   => $validated['region_id'],
        ]);

        return response()->json([
            'message' => 'Office created successfully',
            'data' => [
                'id' => $office->id,
                'office_code' => $office->office_code,
                'office_desc' => $office->office_desc,
                'region_desc' => $office->region?->region_desc ?? '',
            ],
        ], 201);
    }

    /** Archive an office */
    public function archive($id)
    {
        $office = LibOffice::findOrFail($id);
        $office->is_archived = true;
        $office->save();

        return response()->json(['message' => 'Office archived successfully']);
    }

    /** Get all archived offices */
    public function archived()
    {
        $offices = LibOffice::with('region')
            ->where('is_archived', true)
            ->orderBy('office_desc')
            ->get();

        return response()->json($offices);
    }

    /** Restore an archived office */
    public function restore($id)
    {
        $office = LibOffice::findOrFail($id);
        $office->is_archived = false;
        $office->save();

        return response()->json(['message' => 'Office restored successfully']);
    }

    /** Permanently delete an archived office */
    public function forceDelete($id)
    {
        $office = LibOffice::findOrFail($id);
        $office->delete();

        return response()->json(['message' => 'Office permanently deleted']);
    }
}
