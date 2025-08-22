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

        DB::beginTransaction();
        try {
            // Handle file upload
            $uploadPath = null;
            if ($request->hasFile('Upload_Contract')) {
                $uploadPath = $request->file('Upload_Contract')->store('contracts', 'public');
}

            // Create employee record
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

            // Create user account linked to employee
$user = TblUser::create([
    'Username' => $fields['username'],
    'Email_Address' => $fields['email_address'],
    'Password' => Hash::make($fields['password']),
    'Activated' => 0,  // set to 0 by default (inactive)
    'created_by' => Auth::check() ? Auth::id() : null,
    'updated_by' => Auth::check() ? Auth::id() : null,
]);

            DB::commit();

            return response()->json([
                'employee' => $employee,
                'user' => $user,
                'message' => 'Please contact Administrator for Activation of Account'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Registration failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
