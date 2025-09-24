<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Tables\TblUser;
use App\Models\Library\LibEmployee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $fields = $request->validated();

        // 1️⃣ Check for duplicates
        if (TblUser::where('username', $fields['username'])->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Username already exists.'
            ], 422);
        }

        if (TblUser::where('email', $fields['email'])->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email address already exists.'
            ], 422);
        }

        DB::beginTransaction();

        try {
            // 2️⃣ Handle file upload
            $uploadPath = null;
            if ($request->hasFile('upload_contract')) {
                $uploadPath = $request->file('upload_contract')->store('contracts', 'public');
            }

            // 3️⃣ Create tbl_user
            $user = TblUser::create([
                'username'        => $fields['username'],
                'email'           => $fields['email'],
                'password'        => Hash::make($fields['password']),
                'employee_no'    => $fields['employee_no'],
                'honorific'       => $fields['honorific'] ?? null,
                'first_name'      => $fields['first_name'],
                'middle_name'     => $fields['middle_name'] ?? null,
                'last_name'       => $fields['last_name'],
                'suffix'          => $fields['suffix'] ?? null,
                'title'           => $fields['title'] ?? null,
                'sex'             => $fields['sex'] ?? null,
                'position_id'     => $fields['position_id'] ?? null,
                'region_id'       => $fields['region_id'] ?? null,
                'office_id'       => $fields['office_id'] ?? null,
                'division_id'     => $fields['division_id'] ?? null,
                'cluster_id'      => $fields['cluster_id'] ?? null,
                'contact_number'  => $fields['contact_number'] ?? null,
                'address'         => $fields['address'] ?? null,
                'upload_contract' => $uploadPath,
                'user_level_id'   => 0,
                'activated'       => 0,
                'created_by'      => Auth::id() ?? 0,   // ✅ safe fallback
                'updated_by'      => Auth::id() ?? 0,
            ]);

            // 4️⃣ Create lib_employee history
            $employeeHistory = LibEmployee::create([
                'user_id'        => $user->id,
                'honorific'      => $user->honorific,
                'first_name'     => $user->first_name,
                'middle_name'    => $user->middle_name,
                'last_name'      => $user->last_name,
                'employee_no'    => $user->employee_no,   // ✅ added
                'suffix'         => $user->suffix,
                'title'          => $user->title,
                'sex'            => $user->sex,
                'position_id'    => $user->position_id,
                'region_id'      => $user->region_id,
                'office_id'      => $user->office_id,
                'division_id'    => $user->division_id,
                'cluster_id'     => $user->cluster_id,
                'contact_number' => $user->contact_number,
                'address'        => $user->address,
                'upload_contract'=> $user->upload_contract,
                'user_level_id'  => 0,
                'version_no'     => 1,
                'effective_date' => now(),
                'created_by'     => Auth::id() ?? 0,
                'updated_by'     => Auth::id() ?? 0,
            ]);

            // 5️⃣ Assign employee_pk to tbl_user
            $user->employee_id = $employeeHistory->id; // ✅ match your tbl_users schema
            $user->save();

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Registration successful. Please contact Administrator for activation.',
                'user'    => $user
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Registration failed.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
