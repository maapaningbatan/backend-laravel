<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibEmployee;
use App\Models\Tables\TblUser;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
{
    $users = TblUser::leftJoin('lib_positions', 'tbl_users.position_id', '=', 'lib_positions.id')
        ->leftJoin('lib_offices', 'tbl_users.office_id', '=', 'lib_offices.id')
        ->select(
            'tbl_users.id as User_Id',
            'tbl_users.username as Username',
            'tbl_users.first_name as First_Name',
            'tbl_users.middle_name as Middle_Name',
            'tbl_users.last_name as Last_Name',
            'tbl_users.email as Email_Address',
            'tbl_users.activated as Activated',
            'lib_positions.position_desc as position_desc',
            'lib_offices.office_desc as office_desc'
        )
        ->get();

    return response()->json($users);
}

    public function profile(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Not authenticated'], 401);
        }

        $userData = TblUser::select(
            'id as User_Id',
            'username as Username',
            'email as Email_Address',
            'activated as Activated',
            'first_name as First_Name',
            'middle_name as Middle_Name',
            'last_name as Last_Name',
            'sex',
            'position_id as Position',
            'office_id as Office',
            'division_id as Division',
            'cluster_id as Cluster',
            'employee_no as employee_pk',
            'region_id as Region'
        )->where('id', $user->id)->first();

        return response()->json($userData);
    }

    public function getByDivision(Request $request)
    {
        $divisionId = $request->query('division_id');

        if (!$divisionId) {
            return response()->json(['error' => 'division_id is required'], 400);
        }

        $employees = LibEmployee::where('Division', $divisionId)->get();
        return response()->json($employees);
    }

    public function toggleActivation($id)
    {
        $user = TblUser::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->activated = !$user->activated;
        $user->save();

        return response()->json(['Activated' => $user->activated]);
    }
}
