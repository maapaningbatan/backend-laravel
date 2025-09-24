<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Library\LibSupplier;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = LibSupplier::select(
            'Supplier_Id',
            'Supplier_Name',
            'Supplier_Address',
            'Contact_Person',
            'Contact_No',
            'Tin_No'
        )->get();
        return response()->json($suppliers);
    }
}
