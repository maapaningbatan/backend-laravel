<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibEmployee;
use App\Models\Library\LibEmployeeHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = LibEmployee::with([
            'position', 'office', 'division', 'soa', 'soe', 'region', 'cluster',
        ])
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($employees);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_no' => 'required|string|max:50',
            'honorific' => 'nullable|string|max:50',
            'suffix' => 'nullable|string|max:50',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'title' => 'nullable|string|max:50',
            'position_id' => 'required|integer',
            'sex' => 'required|integer',
            'office_id' => 'required|integer',
            'division_id' => 'nullable|integer',
            'soa_id' => 'required|integer',
            'soe_id' => 'required|integer',
            'upload_contract' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $userId = auth()->id() ?? 1;

            $data = $request->only([
                'employee_no',
                'honorific',
                'first_name',
                'middle_name',
                'last_name',
                'suffix',
                'title',
                'sex',
                'position_id',
                'region_id',
                'cluster_id',
                'office_id',
                'division_id',
                'soa_id',
                'soe_id',
                'start_date',
            ]);

            $data['created_by'] = $userId;
            $data['updated_by'] = $userId;

            // Handle contract upload
            if ($request->hasFile('upload_contract')) {
                $path = $request->file('upload_contract')->store('contracts', 'public');
                $data['upload_contract'] = $path;
            }

            // Create the employee
            $employee = LibEmployee::create($data);

            // Store only relevant fields in employee history
            LibEmployeeHistory::create([
                'employee_id' => $employee->id,
                'position_id' => $employee->position_id,
                'office_id' => $employee->office_id,
                'division_id'=>$employee->division_id,
                'soa_id' => $employee->soa_id,
                'soe_id' => $employee->soe_id,
                'start_date' => $employee->start_date,
                'end_date' => $employee->end_date,
                'created_by' => $userId,
                'created_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'message' => '✅ Employee created successfully',
                'employee' => $employee,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => '❌ Failed to create employee',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $employee = LibEmployee::with([
            'position', 'office', 'division', 'soa', 'soe', 'region', 'cluster',
        ])->find($id);

        if (! $employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        return response()->json($employee);
    }

    public function update(Request $request, $id)
    {
        $employee = LibEmployee::find($id);

        if (! $employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'employee_no' => 'required|string|max:50',
            'honorific' => 'nullable|string|max:50',
            'suffix' => 'nullable|string|max:50',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'title' => 'nullable|string|max:50',
            'sex' => 'required|integer',
            'upload_contract' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            $data = $request->only([
                'employee_no',
                'honorific',
                'first_name',
                'middle_name',
                'last_name',
                'suffix',
                'title',
                'sex',
            ]);

            $userId = auth()->id() ?? 1;
            $data['updated_by'] = $userId;

            // Handle file upload (optional)
            if ($request->hasFile('upload_contract')) {
                $path = $request->file('upload_contract')->store('contracts', 'public');
                $data['upload_contract'] = $path;
            }

            // Update employee record only
            $employee->update($data);

            DB::commit();

            return response()->json([
                'message' => '✅ Employee profile updated successfully',
                'employee' => $employee->fresh([
                    'position', 'office', 'division', 'soa', 'soe',
                ]),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => '❌ Failed to update employee profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        $employee = LibEmployee::find($id);

        if (! $employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $userId = auth()->id() ?? 1;

        $employee->deleted_at = now();
        $employee->deleted_by = $userId;
        $employee->save();

        return response()->json([
            'message' => '✅ Employee soft deleted successfully',
        ]);
    }
    public function destroyHistory($employeeId, $historyId)
{
    $history = LibEmployeeHistory::where('employee_id', $employeeId)
        ->where('id', $historyId)
        ->first();

    if (! $history) {
        return response()->json(['message' => 'History record not found'], 404);
    }

    $userId = auth()->id() ?? 1;
    $history->deleted_at = now();
    $history->deleted_by = $userId;
    $history->save();

    return response()->json(['message' => '✅ History record deleted successfully']);
}

}
