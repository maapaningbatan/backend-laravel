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

        $ris = RIS::create(array_merge($request->all(), ['status' => 'Pending']));

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
