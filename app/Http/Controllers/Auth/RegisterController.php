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

        // Prevent duplicate username/email BEFORE DB insert
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

            // ✅ Create employee record
            $employee = LibEmployee::create([
                'Honorifics' => $fields['honorifics'] ?? null,
                'First_Name' => $fields['first_name'],
                'Middle_Name' => $fields['middle_name'] ?? null,
                'Last_Name' => $fields['last_name'],
                'Suffix' => $fields['suffix'] ?? null,
                'Title' => $fields['title'] ?? null,
                'Sex' => $fields['sex'] ?? null,
                'Contact_Number' => $fields['contact_number'] ?? null,
                'Address' => $fields['address'] ?? null,
                'Employee_Id' => $fields['employee_id'] ?? null,
                'Position' => $fields['position'] ?? null,
                'Region' => $fields['region'] ?? null,
                'Office' => $fields['office'] ?? null,
                'Division' => $fields['division'] ?? null,
                'Cluster' => $fields['cluster'] ?? null,
                'SOA' => $fields['soa'] ?? null,
                'SOE' => $fields['soe'] ?? null,
                'User_Level' => $fields['user_level'] ?? null,
                'Upload_Contract' => $uploadPath,
                'created_by' => Auth::check() ? Auth::id() : null,
                'updated_by' => Auth::check() ? Auth::id() : null,
            ]);

            // ✅ Create user account linked to employee
            $user = TblUser::create([
                'Username' => $fields['username'],
                'Email_Address' => $fields['email_address'],
                'Password' => Hash::make($fields['password']),
                'Employee_PK' => $employee->Employee_PK,
                'Activated' => 0,  // default inactive
                'created_by' => Auth::check() ? Auth::id() : null,
                'updated_by' => Auth::check() ? Auth::id() : null,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Please contact Administrator for activation of your account.',
                'employee' => $employee,
                'user' => $user,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
