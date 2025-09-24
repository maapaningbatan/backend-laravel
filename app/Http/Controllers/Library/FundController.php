<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibFund;

class FundController extends Controller
{
    public function index()
    {
        $funds = LibFund::select(
            'Fund_Id',
            'Fund_Code',
            'Fund_Description'

        )->get();
        return response()->json($funds);
    }
}
