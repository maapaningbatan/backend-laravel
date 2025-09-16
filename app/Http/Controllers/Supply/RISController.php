<?php

namespace App\Http\Controllers\Supply;

use App\Models\Supply\RIS; // ✅ Correct import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class RISController extends Controller
{
    public function index()
    {
        $ris = RIS::orderBy('ris_date', 'desc')->get();
        return response()->json($ris);
    }

public function store(Request $request)
{
    $user = auth('sanctum')->user(); // or auth()->user()

    if (!$user) {
        return response()->json(['error' => 'Not authenticated'], 401);
    }

    $validator = Validator::make($request->all(), [
        'responsibility_center' => 'required|string|max:255',
        'purpose' => 'nullable|string',
        'requested_by' => 'required|string|max:255',
        'received_by' => 'nullable|string|max:255',
        'approved_by' => 'nullable|string|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Get region of logged-in user
    $regionId = $user->Region ?? null;
    if (!$regionId) {
        return response()->json(['error' => 'User has no region assigned'], 422);
    }

    $regionCode = \App\Models\Library\LibRegion::where('Region_ID', $regionId)->value('Region_Code') ?? 'REG00';

    $ris = RIS::create([
        'responsibility_center' => $request->responsibility_center,
        'purpose' => $request->purpose,
        'requested_by' => $request->requested_by,
        'received_by' => $request->received_by,
        'approved_by' => $request->approved_by,
        'status' => 'Pending',
        'region' => $regionCode,
        'ris_number' => null,
    ]);

    $risNumber = RIS::generateRISNumber($regionCode);
    $ris->update(['ris_number' => $risNumber]);
    dd($risNumber);

    return response()->json($ris, 201);
}



    public function show($id)
    {
        $ris = RIS::findOrFail($id);
        return response()->json($ris);
    }

    public function update(Request $request, $id)
    {
        $ris = RIS::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'ris_number' => 'required|string|max:255',
            'responsibility_center' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'office' => 'required|string|max:255',
            'fund_cluster' => 'nullable|string|max:255',
            'ris_date' => 'required|date',
            'purpose' => 'nullable|string',
            'requested_by' => 'required|string|max:255',
            'received_by' => 'nullable|string|max:255',
            'approved_by' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $ris->update($request->all());
        return response()->json($ris);
    }

    public function destroy($id)
    {
        $ris = RIS::findOrFail($id);
        $ris->delete();
        return response()->json(['message' => 'RIS deleted successfully']);
    }

    public function approve($id)
    {
        $ris = RIS::findOrFail($id);

        if ($ris->status === 'Approved') {
            return response()->json(['message' => 'Already approved'], 400);
        }

        $ris->update(['status' => 'Approved']);
        return response()->json(['message' => 'RIS approved successfully']);
    }
}
