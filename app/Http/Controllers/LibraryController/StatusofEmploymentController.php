<?php

namespace App\Http\Controllers\LibraryController;

use App\Http\Controllers\Controller;
use App\Models\Library\LibStatusOfEmployment;

class StatusOfEmploymentController extends Controller
{
    // Return all status of appointments for dropdown
    public function index()
    {
        $statusList = LibStatusOfEmployment::select('SOE_Id', 'Soe')->get();
        return response()->json($statusList);
    }

    // You can add more CRUD methods if needed
}
