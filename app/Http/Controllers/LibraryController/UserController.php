<?php

namespace App\Http\Controllers\LibraryController;

use App\Http\Controllers\Controller;
use App\Models\Tables\TblUser;
use Illuminate\Http\Request; // ✅ Add this

class UserController extends Controller
{
    public function index()
    {
        // Get all users
        $users = TblUser::all();

        return response()->json($users);
    }

public function profile(Request $request)
{
    $user = $request->user(); // logged-in user via Sanctum

    if (!$user) {
        return response()->json(['message' => 'Not authenticated'], 401);
    }

    // Select only the fields you want to return
    $userData = TblUser::select(
        'User_Id',
        'Username',
        'Email_Address',
        'Activated',
        'First_Name',
        'Middle_Name',
        'Last_Name',
        'Sex',
        'Position',
        'Office',
        'Division',
        'Cluster',
        'employee_pk'
    )->where('User_Id', $user->User_Id)->first();

    return response()->json($userData);
}

}
