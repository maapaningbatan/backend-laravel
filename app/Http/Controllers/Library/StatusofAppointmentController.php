<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibStatusOfAppointment;

class StatusOfAppointmentController extends Controller
{
    // Return all status of appointments for dropdown
    public function index()
    {
        $statusList = LibStatusOfAppointment::select('SOA_Id', 'Status_of_Appointment')->get();
        return response()->json($statusList);
    }

    // You can add more CRUD methods if needed
}
