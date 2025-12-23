<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibEmployeeAssignment;
use Illuminate\Http\Request;

class EmployeeAssignmentController extends Controller
{
    /**
     * Get all assignments for a specific employee.
     */
    public function index($employeeId)
    {
        $empAssignment = LibEmployeeAssignment::with(['office', 'division'])
            ->where('employee_id', $employeeId)
            ->whereNull('deleted_at')
            ->select(
                'id',
                'employee_id',
                'designation',
                'office_id',
                'division_id',
                'start_date',
                'end_date',
                'status',
                'created_by',
                'created_at',
                'updated_by',
                'updated_at',
                'deleted_by',
                'deleted_at'
            )
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json($empAssignment);
    }

    /**
     * Store a new employee assignment.
     */
   public function store(Request $request)
{
    $validated = $request->validate([
        'employee_id' => 'required|integer|exists:lib_employees,id',
        'designation' => 'nullable|string|max:255',
        'office_id' => 'nullable|integer|exists:lib_offices,id',
        'division_id' => 'nullable|integer|exists:lib_divisions,id',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'status' => 'required|boolean',
    ]);

    $validated['created_by'] = auth()->id();

    $assignment = LibEmployeeAssignment::create($validated);

    // 🔹 Return with relationships so Vue can display without refresh
    $assignment = LibEmployeeAssignment::with(['office', 'division'])
        ->find($assignment->id);

    return response()->json($assignment, 201);
}

public function update(Request $request, $employeeId, $assignmentId)
{
    $assignment = LibEmployeeAssignment::where('id', $assignmentId)
        ->where('employee_id', $employeeId)
        ->firstOrFail();

    $validated = $request->validate([
        'designation' => 'nullable|string|max:255',
        'office_id' => 'nullable|integer|exists:lib_offices,id',
        'division_id' => 'nullable|integer|exists:lib_divisions,id',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'status' => 'required|boolean',
    ]);

    $validated['employee_id'] = $employeeId;
    $validated['updated_by'] = auth()->id();

    $assignment->update($validated);

    // 🔹 Re-fetch with relationships before returning
    $assignment = LibEmployeeAssignment::with(['office', 'division'])
        ->find($assignment->id);

    return response()->json($assignment);
}
    public function destroy($employeeId, $assignmentId)
    {
        $assignment = LibEmployeeAssignment::where('id', $assignmentId)
            ->where('employee_id', $employeeId)
            ->firstOrFail();

        $assignment->deleted_by = auth()->id();
        $assignment->save();

        $assignment->delete();

        return response()->json(['message' => 'Assignment deleted successfully']);
    }
}
