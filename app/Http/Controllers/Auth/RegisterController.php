<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Library\LibEmployee;
use App\Models\Tables\TblUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $fields = $request->validated();

        // ✅ Prevent duplicate username/email BEFORE DB insert
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
            // ✅ Handle file upload
            $uploadPath = null;
            if ($request->hasFile('Upload_Contract')) {
                $uploadPath = $request->file('Upload_Contract')->store('contracts', 'public');
            }

            // ✅ Create user record (tbl_user is now master record)
            $user = TblUser::create([
                'Username'       => $fields['username'],
                'Email_Address'  => $fields['email_address'],
                'Password'       => Hash::make($fields['password']),
                'Employee_Id'    => $fields['employee_id'] ?? null,
                'Honorifics'     => $fields['honorifics'] ?? null,
                'First_Name'     => $fields['first_name'],
                'Middle_Name'    => $fields['middle_name'] ?? null,
                'Last_Name'      => $fields['last_name'],
                'Suffix'         => $fields['suffix'] ?? null,
                'Title'          => $fields['title'] ?? null,
                'Sex' => $fields['sex'] ?? null,
                'Position'       => $fields['position'] ?? null,
                'Region'         => $fields['region'] ?? null,
                'Office'         => $fields['office'] ?? null,
                'Division'       => $fields['division'] ?? null,
                'Cluster'        => $fields['cluster'] ?? null,
                'Contact_Number' => $fields['contact_number'] ?? null,
                'Address'        => $fields['address'] ?? null,
                'Upload_Contract'=> $uploadPath,
                'User_Level'     => $fields['user_level'] ?? 0,
                'Activated'      => 0, // default inactive
                'created_by'     => Auth::check() ? Auth::id() : null,
                'updated_by'     => Auth::check() ? Auth::id() : null,
            ]);

            // ✅ Insert history row in lib_employee
            LibEmployee::create([
                'User_Id'        => $user->User_Id,
                'Employee_Id'    => $user->Employee_Id,
                'Honorifics'     => $user->Honorifics,
                'First_Name'     => $user->First_Name,
                'Middle_Name'    => $user->Middle_Name,
                'Last_Name'      => $user->Last_Name,
                'Suffix'         => $user->Suffix,
                'Title'          => $user->Title,
                'Sex'            => $fields['sex'] ?? null,
                'Position'       => $user->Position,
                'Region'         => $user->Region,
                'Office'         => $user->Office,
                'Division'       => $user->Division,
                'Cluster'        => $user->Cluster,
                'Contact_Number' => $user->Contact_Number,
                'Address'        => $user->Address,
                'Upload_Contract'=> $user->Upload_Contract,
                'SOA'            => $fields['soa'] ?? null,
                'SOE'            => $fields['soe'] ?? null,
                'User_Level'     => $user->User_Level,
                'version_no'     => 1,
                'effective_date' => now(),
                'created_by'     => $user->created_by,
                'updated_by'     => $user->updated_by,
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Please contact Administrator for activation of your account.',
                'user'    => $user,
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
