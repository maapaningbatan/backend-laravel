<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property\SemiExCard;

class SemiExCardController extends Controller
{
    /**
     * List all semi-expendable cards (paginated)
     */
    public function index(Request $request)
    {
        $query = SemiExCard::with(['supply.category', 'supply.unit']);

        $perPage = $request->input('per_page', 10);
        $data = $query->paginate($perPage);

        return response()->json($data);
    }

    /**
     * Fetch a single semi-expendable card along with all its transactions
     */
    public function show($id)
    {
        // Fetch main card
        $card = SemiExCard::with('supply.category')->find($id);
        if (!$card) {
            return response()->json(['message' => 'Card not found'], 404);
        }

        // Fetch all transactions for the same supply_id
        $transactions = SemiExCard::where('supply_id', $card->supply_id)
            ->orderBy('transaction_date', 'asc')
            ->get();

        // Include latest balance in the main card
        $latestTransaction = $transactions->last();
        $card->latest_balance = $latestTransaction->balance ?? $card->balance;

        return response()->json([
            'supply' => $card,
            'semiCards' => $transactions,
        ]);
    }
}
