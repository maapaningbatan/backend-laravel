<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Library\LibPropertyChief;

class PropertyChiefController extends Controller
{
   public function index()
{
    $chiefs = DB::table('lib_property_chiefs as c')
        ->join('lib_employees as e', 'c.employee_id', '=', 'e.id')
        ->where('c.is_active', 1) // only active chiefs
        ->select(
            'c.id',
            'c.employee_id',
            'c.start_date',
            'c.end_date',
            'c.is_active',
            'e.first_name',
            'e.middle_name',
            'e.last_name',
            DB::raw("CONCAT(e.first_name, ' ', COALESCE(e.middle_name, ''), ' ', e.last_name) as employee_name")
        )
        ->orderBy('e.last_name')
        ->get();

    return response()->json($chiefs);
}



    public function store(Request $request) {
        return LibPropertyChief::create($request->all());
    }

    public function show($id) {
        return LibPropertyChief::findOrFail($id);
    }

    public function update(Request $request, $id) {
        $chief = LibPropertyChief::findOrFail($id);
        $chief->update($request->all());
        return $chief;
    }

    public function destroy($id) {
        $chief = LibPropertyChief::findOrFail($id);
        $chief->delete();
        return response()->noContent();
    }
}

