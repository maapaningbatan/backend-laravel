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

    DB::beginTransaction();

    try {
        // 1️⃣ Handle optional file upload
        $uploadPath = $request->hasFile('upload_contract')
            ? $request->file('upload_contract')->store('contracts', 'public')
            : null;

        // 2️⃣ Create tbl_user only
        $user = TblUser::create([
            'username'        => $fields['username'],
            'email'           => $fields['email'],
            'password'        => Hash::make($fields['password']),
            'first_name'      => $fields['first_name'],
            'middle_name'     => $fields['middle_name'] ?? null,
            'last_name'       => $fields['last_name'],
            'suffix'          => $fields['suffix'] ?? null,
            'position_id'     => $fields['position_id'] ?? null,
            'region_id'       => $fields['region_id'] ?? null,
            'office_id'       => $fields['office_id'] ?? null,
            'division_id'     => $fields['division_id'] ?? null,
            'user_level_id'   => 0,
            'activated'       => 0, // requires admin activation
            'upload_contract' => $uploadPath,
            'created_by'      => Auth::id() ?? 0,
            'updated_by'      => Auth::id() ?? 0,
        ]);

        DB::commit();

        return response()->json([
            'status'  => 'success',
            'message' => 'Registration successful. Please wait for admin activation.',
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
