<?php

namespace App\Http\Controllers\LibraryController;

use App\Http\Controllers\Controller;
use App\Models\Library\LibDivision;

class DivisionController extends Controller
{
    public function index()
    {
        return LibDivision::whereNull('date_deleted')
            ->orderBy('Division_Desc')
            ->select('Division_Id', 'Division_Desc', 'Office_Id')
            ->get();
    }

    public function byOffice($officeId)
    {
        return LibDivision::where('Office_Id', $officeId)
            ->whereNull('date_deleted')
            ->orderBy('Division_Desc')
            ->select('Division_Id', 'Division_Desc', 'Office_Id')
            ->get();
    }
}
