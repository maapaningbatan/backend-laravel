<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property\SemiExCard;

class SemiExCardController extends Controller
{
    public function index(Request $request)
{
    $query = SemiExCard::with('supply.category');

    // Optional: search
    if ($request->has('search')) {
        $search = $request->input('search');
        $query->whereHas('supply', function($q) use ($search) {
            $q->where('Stock_Number', 'like', "%{$search}%")
              ->orWhere('Description', 'like', "%{$search}%");
        });
    }

    // Optional: status filter
    if ($request->has('status')) {
        if ($request->status === 'active') $query->where('balance', '>', 0);
        elseif ($request->status === 'inactive') $query->where('balance', '=', 0);
    }

    $perPage = $request->input('per_page', 10);
    $data = $query->paginate($perPage);

    return response()->json($data);
}
}
