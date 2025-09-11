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
    return response()->json($request->user());
}

}
