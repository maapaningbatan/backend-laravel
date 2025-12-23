<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property\PropertyCard;
use App\Models\Library\LibSupply;

class PropertyCardController extends Controller
{
    /**
     * 📋 List all property cards with optional filters
     */
    public function index(Request $request)
    {
        $query = PropertyCard::with(['supply.category', 'supply.unit']);

        // 🔍 Search by stock number or description

        // 📄 Pagination
        $perPage = $request->input('per_page', 10);
        return response()->json(
            $query->orderBy('created_at', 'desc')->paginate($perPage)
        );
    }
    public function show($supplyId)
    {
        $propertyCards = PropertyCard::with(['supply.category', 'supply.unit'])
            ->where('supply_id', $supplyId)
            ->orderBy('transaction_date', 'asc')
            ->get();

        $supply = LibSupply::find($supplyId);

        if (!$supply) {
            return response()->json(['message' => 'Supply not found'], 404);
        }

        return response()->json([
            'supply' => [
                'id' => $supply->id,
                'supplies_desc' => $supply->supplies_desc,
                'stock_no' => $supply->stock_no,
                'supplies_reorder_point' => $supply->supplies_reorder_point,
                'unit_value' => $supply->unit_value,
                'unit' => optional($supply->unit)->unit_desc,
                'category' => optional($supply->category)->category_desc,
            ],
            'property_cards' => $propertyCards
        ]);
    }
}
