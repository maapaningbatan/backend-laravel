<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property\PropertyCard;
use App\Models\Library\LibSupply;

class PropertyCardController extends Controller
{
    /**
     * List all property cards with filters
     */
    public function index(Request $request)
    {
        // eager load supply + unit + category
        $query = PropertyCard::with(['supply.category', 'supply.unit']);

        // 🔍 Search by Stock Number or Description
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('supply', function ($q) use ($search) {
                $q->where('StockNo', 'like', "%{$search}%")
                  ->orWhere('Supplies_Desc', 'like', "%{$search}%");
            });
        }

        // ✅ Active/Inactive filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('balance', '>', 0);
            } elseif ($request->status === 'inactive') {
                $query->where('balance', '=', 0);
            }
        }

        // 📄 Pagination (default 10 per page)
        $perPage = $request->input('per_page', 10);

        return response()->json(
            $query->orderBy('created_at', 'desc')->paginate($perPage)
        );
    }
   public function show($supplyId)
{
    // Fetch property card entries for the supply
    $propertyCards = PropertyCard::with('supply')
        ->where('supply_id', $supplyId)
        ->orderBy('transaction_date', 'asc')
        ->get();

    // Fetch the supply info
    $supply = LibSupply::find($supplyId);

    if (!$supply) {
        return response()->json(['message' => 'Supply not found'], 404);
    }

    return response()->json([
        'supply' => [
            'supply_id' => $supply->SuppliesID,   // ✅ match your schema
            'Supplies_Desc' => $supply->Supplies_Desc,
            'StockNo' => $supply->StockNo,
            'Supplies_ReOrder_PT' => $supply->Supplies_ReOrder_PT,
            'Supplies_Qty' => $supply->Supplies_Qty,
            'supplies_nonstock_qty' => $supply->supplies_nonstock_qty,
        ],
        'propertyCards' => $propertyCards // ✅ renamed for consistency
    ]);
}

}
