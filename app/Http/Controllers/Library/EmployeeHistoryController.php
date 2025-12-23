<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibEmployee;
use App\Models\Library\LibEmployeeHistory;
use Illuminate\Http\Request;

class EmployeeHistoryController extends Controller
{
    public function index($employeeId)
    {
        $emphistory = LibEmployeeHistory::with(['position', 'office', 'division', 'soa', 'soe'])
            ->where('employee_id', $employeeId)
            ->whereNull('deleted_at')
            ->select(
                'id',
                'employee_id',
                'position_id',
                'office_id',
                'division_id',
                'soa_id',
                'soe_id',
                'start_date',
                'end_date',
                'clearance',
                'remarks',
                'created_by',
                'created_at',
                'deleted_by',
                'deleted_at',
                'updated_by'
            )
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json($emphistory);
    }

 public function store(Request $request, $employeeId)
{
    $data = $request->validate([
        'position_id' => 'required|exists:lib_positions,id',
        'office_id' => 'required|exists:lib_offices,id',
        'division_id' => 'nullable|exists:lib_divisions,id',
        'soa_id' => 'required|exists:lib_soa,id',
        'soe_id' => 'required|exists:lib_soe,id',
        'start_date' => 'required|date',
        'remarks' => 'nullable|string',
    ]);

    $data['employee_id'] = $employeeId;

    $history = LibEmployeeHistory::create($data);

    LibEmployee::where('id', $employeeId)->update([
        'position_id' => $data['position_id'],
        'office_id' => $data['office_id'],
        'division_id' => $data['division_id'] ?? null,
        'soa_id' => $data['soa_id'],
        'soe_id' => $data['soe_id'],
        'start_date' => $data['start_date'],
    ]);

    // Return fresh record with relationships
    $history = $history->fresh(['position', 'office', 'division', 'soa', 'soe']);

    return response()->json($history, 201); // ✅ same as assignments
}


  public function update(Request $request, $employeeId, $historyId)
{
    $history = LibEmployeeHistory::where('employee_id', $employeeId)
        ->where('id', $historyId)
        ->firstOrFail();

    // Validate input
    $data = $request->validate([
        'position_id' => 'required|exists:lib_positions,id',
        'office_id' => 'required|exists:lib_offices,id',
        'division_id' => 'nullable|exists:lib_divisions,id',
        'soa_id' => 'required|exists:lib_soa,id',
        'soe_id' => 'required|exists:lib_soe,id',
        'start_date' => 'required|date',
        'end_date' => 'nullable|date',
        'remarks' => 'nullable|string',
    ]);

    // Convert empty strings to null (especially for division)
    $data['division_id'] = $request->input('division_id') === '' ? null : $request->input('division_id');

    // Track who updated the record
    $data['updated_by'] = auth()->id();

    // Handle file upload
    if ($request->hasFile('clearance')) {
        $file = $request->file('clearance');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('clearances', $filename, 'public');
        $data['clearance'] = $filename;
    }

    // Update history record
    $history->update($data);

    // If end_date is null (currently employed), also update employee's main record
    if (empty($data['end_date'])) {
        $employee = LibEmployee::findOrFail($employeeId);

        $employee->update([
            'position_id' => $data['position_id'],
            'office_id' => $data['office_id'],
            'division_id' => $data['division_id'],
            'soa_id' => $data['soa_id'],
            'soe_id' => $data['soe_id'],
            'start_date' => $data['start_date'],
            'updated_by' => auth()->id(),
        ]);
    }

   return response()->json(
    $history->fresh(['position', 'office', 'division', 'soa', 'soe'])
);

}




}
