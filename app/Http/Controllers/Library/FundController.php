<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Library\LibFund;

class FundController extends Controller
{
    // List active funds
    public function index()
    {
        $funds = LibFund::where('is_archived', 0)->get();
        return response()->json($funds);
    }

    // List archived funds
    public function archived()
    {
        $funds = LibFund::where('is_archived', 1)->get();
        return response()->json($funds);
    }

    // Show single fund
    public function show($id)
    {
        $fund = LibFund::findOrFail($id);
        return response()->json($fund);
    }

    // Create new fund
    public function store(Request $request)
    {
        $request->validate([
            'fund_code' => 'required|string|max:100|unique:lib_funds,fund_code',
            'fund_desc' => 'required|string|max:255',
        ]);

        $fund = LibFund::create([
            'fund_code' => $request->fund_code,
            'fund_desc' => $request->fund_desc,
            'is_archived' => 0,
        ]);

        return response()->json($fund, 201);
    }

    // Update fund
    public function update(Request $request, $id)
    {
        $fund = LibFund::findOrFail($id);

        $request->validate([
            'fund_code' => 'required|string|max:100|unique:lib_funds,fund_code,' . $id,
            'fund_desc' => 'required|string|max:255',
        ]);

        $fund->update([
            'fund_code' => $request->fund_code,
            'fund_desc' => $request->fund_desc,
        ]);

        return response()->json($fund);
    }

    // Archive fund
    public function archive($id)
    {
        $fund = LibFund::findOrFail($id);
        $fund->update(['is_archived' => 1]);

        return response()->json(['message' => 'Archived successfully']);
    }

    // Restore fund
    public function restore($id)
    {
        $fund = LibFund::findOrFail($id);
        $fund->update(['is_archived' => 0]);

        return response()->json(['message' => 'Restored successfully']);
    }

    // Permanent delete
    public function forceDelete($id)
    {
        $fund = LibFund::findOrFail($id);
        $fund->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
