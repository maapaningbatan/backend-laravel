<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Library\LibSupplyCustodian;

class SupplyCustodianController extends Controller
{

   public function index()
{
    return LibSupplyCustodian::with('employee')
        ->where('is_active', 1)
        ->whereDate('start_date', '<=', now())
        ->where(function ($q) {
            $q->whereNull('end_date')
              ->orWhereDate('end_date', '>=', now());
        })
        ->orderBy('employee_id')
        ->get();
}



    public function show($id)
    {
        return LibSupplyCustodian::with('employee')->findOrFail($id);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'is_active' => 'nullable|integer',
        ]);

        $data['created_by'] = auth()->id();

        $record = LibSupplyCustodian::create($data);

        return response()->json($record, 201);
    }

    public function update(Request $request, $id)
    {
        $record = LibSupplyCustodian::findOrFail($id);

        $data = $request->validate([
            'employee_id' => 'nullable|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'is_active' => 'nullable|integer',
        ]);

        $data['updated_by'] = auth()->id();

        $record->update($data);

        return response()->json($record);
    }


    public function destroy($id)
    {
        $record = LibSupplyCustodian::findOrFail($id);

        $record->deleted_by = auth()->id();
        $record->save();
        $record->delete();

        return response()->json(['message' => 'Record deleted']);
    }
}
