<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibStatusofAppointment;
use Illuminate\Http\Request;

class StatusofAppointmentController extends Controller
{

    // List active SOAs
    public function index()
    {
        $data = LibStatusofAppointment::where('is_archived', 0)->get();
        return response()->json($data);
    }

    // List archived SOAs
    public function archived()
    {
        $data = LibStatusofAppointment::where('is_archived', 1)->get();
        return response()->json($data);
    }

    // Show single SOA
    public function show($id)
    {
        $soa = LibStatusofAppointment::findOrFail($id);
        return response()->json($soa);
    }

    // Create new SOA
    public function store(Request $request)
    {
        $request->validate([
            'status_appointment' => 'required|string|max:255|unique:lib_soa,status_appointment',
        ]);

        $soa = LibStatusofAppointment::create([
            'status_appointment' => $request->status_appointment,
            'is_archived' => 0,
        ]);

        return response()->json($soa, 201);
    }

    // Update SOA
    public function update(Request $request, $id)
    {
        $soa = LibStatusofAppointment::findOrFail($id);

        $request->validate([
            'status_appointment' => 'required|string|max:255|unique:lib_soa,status_appointment,' . $id,
        ]);

        $soa->update(['status_appointment' => $request->status_appointment]);

        return response()->json($soa);
    }

    // Archive SOA
    public function archive($id)
    {
        $soa = LibStatusofAppointment::findOrFail($id);
        $soa->update(['is_archived' => 1]);

        return response()->json(['message' => 'Archived successfully']);
    }

    // Restore SOA
    public function restore($id)
    {
        $soa = LibStatusofAppointment::findOrFail($id);
        $soa->update(['is_archived' => 0]);

        return response()->json(['message' => 'Restored successfully']);
    }

    // Permanent delete
    public function forceDelete($id)
    {
        $soa = LibStatusofAppointment::findOrFail($id);
        $soa->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
