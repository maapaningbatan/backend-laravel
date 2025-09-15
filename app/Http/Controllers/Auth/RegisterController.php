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
        if (TblUser::where('Username', $fields['username'])->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Username already exists.'
            ], 422);
        }

        if (TblUser::where('Email_Address', $fields['email_address'])->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email address already exists.'
            ], 422);
        }

        DB::beginTransaction();

        try {
            // 2️⃣ Handle file upload
            $uploadPath = null;
            if ($request->hasFile('Upload_Contract')) {
                $uploadPath = $request->file('Upload_Contract')->store('contracts', 'public');
            }

            // 3️⃣ Create tbl_user
            $user = TblUser::create([
                'Username'       => $fields['username'],
                'Email_Address'  => $fields['email_address'],
                'Password'       => Hash::make($fields['password']),
                'First_Name'     => $fields['first_name'],
                'Middle_Name'    => $fields['middle_name'] ?? null,
                'Last_Name'      => $fields['last_name'],
                'Sex'            => $fields['sex'] ?? null,
                'Position'       => $fields['position'] ?? null,
                'Region'         => $fields['region'] ?? null,
                'Office'         => $fields['office'] ?? null,
                'Division'       => $fields['division'] ?? null,
                'Cluster'        => $fields['cluster'] ?? null,
                'Contact_Number' => $fields['contact_number'] ?? null,
                'Address'        => $fields['address'] ?? null,
                'Upload_Contract'=> $uploadPath,
                'User_Level'     => $fields['user_level'] ?? 0,
                'Activated'      => 0,
                'created_by'     => Auth::id(),
                'updated_by'     => Auth::id(),
            ]);

            // 4️⃣ Create lib_employee history
            $employeeHistory = LibEmployee::create([
                'User_Id'        => $user->User_Id,
                'First_Name'     => $user->First_Name,
                'Middle_Name'    => $user->Middle_Name,
                'Last_Name'      => $user->Last_Name,
                'Sex'            => $user->Sex,
                'Position'       => $user->Position,
                'Region'         => $user->Region,
                'Office'         => $user->Office,
                'Division'       => $user->Division,
                'Cluster'        => $user->Cluster,
                'Contact_Number' => $user->Contact_Number,
                'Address'        => $user->Address,
                'Upload_Contract'=> $user->Upload_Contract,
                'User_Level'     => $user->User_Level,
                'version_no'     => 1,
                'effective_date' => now(),
                'created_by'     => $user->created_by,
                'updated_by'     => $user->updated_by,
            ]);

            // 5️⃣ Assign employee_pk to tbl_user
            $user->employee_pk = $employeeHistory->Employee_PK;
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
