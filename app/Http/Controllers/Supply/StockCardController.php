<?php

namespace App\Http\Controllers\Supply;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supply\StockCard;
use App\Models\Library\LibSupply;

class StockCardController extends Controller
{
    /**
     * Get stock card info for a supply
     * GET /api/stock/card/{id}
     */
    public function show($id)
    {
        // Fetch the StockCard entries for the supply
        $stockcards = StockCard::with('supply')
            ->where('supply_id', $id)
            ->orderBy('transaction_date', 'asc')
            ->get();

        // Fetch the supply info
        $supply = LibSupply::find($id);

        if (!$supply) {
            return response()->json(['message' => 'Supply not found'], 404);
        }

        return response()->json([
            'supply' => [
                'SuppliesID' => $supply->SuppliesID,
                'Supplies_Desc' => $supply->Supplies_Desc,
                'StockNo' => $supply->StockNo,
                'Supplies_ReOrder_PT' => $supply->Supplies_ReOrder_PT,
                'Supplies_Qty' => $supply->Supplies_Qty,
                'supplies_nonstock_qty' => $supply->supplies_nonstock_qty,
            ],
            'stockcards' => $stockcards
        ]);
    }
}
