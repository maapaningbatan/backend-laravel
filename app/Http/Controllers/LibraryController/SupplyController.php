<?php

namespace App\Http\Controllers\LibraryController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Library\LibSupply;
use App\Models\Library\LibCategory;

class SupplyController extends Controller
{
  public function index()
{
    $supplies = LibSupply::with('category','unit')->get();
    return response()->json($supplies);
}



}
