<?php

namespace App\Http\Controllers\Supply;

use Log;
use App\Models\Supply\RIS;
use App\Models\Supply\RisItem;
use Illuminate\Http\Request;
use App\Models\Library\LibRegion;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RISController extends Controller
{
    public function index()
    {
        $ris = RIS::orderBy('ris_date', 'desc')->get();
        return response()->json($ris);
    }

// Generate RIS number: YYYYMMRegionCode0001


public function store(Request $request)
{
    // Validate input
    $validator = Validator::make($request->all(), [
        'responsibility_center' => 'required|string',
        'region' => 'required|string', // full name sent from frontend (Region_Desc)
        'office' => 'required|string',
        'fund_cluster' => 'required|string',
        'ris_date' => 'required|date',
        'purpose' => 'required|string',
        'requested_by' => 'required|string',
        'received_by' => 'required|string',
        'approved_by' => 'required|string',
        'items' => 'required|array|min:1',
        'items.*.supply_id' => 'required|integer',
        'items.*.unit_id' => 'required|integer',
        'items.*.quantity_requested' => 'required|integer|min:1',
        'items.*.description' => 'nullable|string',
        'items.*.remarks' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        DB::beginTransaction();

        // Fetch region code from lib_region using the full name
        $regionRecord = \App\Models\Library\LibRegion::where('Region_Desc', $request->region)->first();
        $regionCode = $regionRecord ? $regionRecord->Region : strtoupper(substr($request->region, 0, 2));

        // Generate RIS number: YYYY-MM-REGIONCODE-XXXX
        $yearMonth = now()->format('Y-m'); // e.g., "2025-09"

        $latestRIS = RIS::where('region', $request->region)
            ->whereYear('ris_date', now()->year)
            ->whereMonth('ris_date', now()->month)
            ->latest('ris_id')
            ->first();

        $nextNumber = $latestRIS
            ? intval(substr($latestRIS->ris_number, -4)) + 1
            : 1;

        $risNumber = $yearMonth . '-' . $regionCode . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // Save RIS
        $ris = RIS::create([
            'ris_number' => $risNumber,
            'responsibility_center' => $request->responsibility_center,
            'region' => $request->region,
            'office' => $request->office,
            'fund_cluster' => $request->fund_cluster,
            'ris_date' => $request->ris_date,
            'purpose' => $request->purpose,
            'requested_by' => $request->requested_by,
            'received_by' => $request->received_by,
            'approved_by' => $request->approved_by,
            'status' => 'pending', // default
        ]);

        // Prepare RIS items for bulk insert
        $risItems = array_map(function ($item) use ($ris) {
            return [
                'ris_id' => $ris->ris_id,
                'supply_id' => $item['supply_id'],
                'unit_id' => $item['unit_id'],
                'quantity_requested' => $item['quantity_requested'],
                'quantity_issued' => $item['quantity_issued'] ?? null,
                'description' => $item['description'] ?? null,
                'remarks' => $item['remarks'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $request->items);

        RISItem::insert($risItems);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => '✅ RIS submitted successfully!',
            'ris_number' => $risNumber,
            'data' => $ris->load('items')
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => '❌ Failed to submit RIS.',
            'error' => $e->getMessage(),
        ], 500);
    }
}
}

