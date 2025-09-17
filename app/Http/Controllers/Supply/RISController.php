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
    // 1️⃣ Validate request
    $validator = Validator::make($request->all(), [
        'responsibility_center' => 'required|string',
        'region_id' => 'required|integer|exists:lib_region,Region_Id',
        'office_id' => 'required|integer|exists:lib_office,Office_Id',
        'fund_cluster' => 'required|string',
        'ris_date' => 'required|date',
        'purpose' => 'required|string',
        'requested_by_id' => 'required|integer|exists:tbl_user,User_Id',
        'received_by_id' => 'required|integer|exists:tbl_user,User_Id',
        'approved_by_id' => 'required|integer|exists:tbl_user,User_Id',
        'items' => 'required|array|min:1',
        'items.*.supply_id' => 'required|integer|exists:lib_supplieslist,SuppliesID',
        'items.*.unit_id' => 'required|integer|exists:lib_unit,Unit_Id',
        'items.*.quantity_requested' => 'required|integer|min:1',
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

        // 2️⃣ Fetch region for RIS number
        $region = DB::table('lib_region')->where('Region_Id', $request->region_id)->first();
        if (!$region) {
            throw new \Exception("Region not found (ID: {$request->region_id})");
        }

        $regionCode = strtoupper(substr($region->Region_Desc, 0, 2));

        // 3️⃣ Generate RIS number
        $yearMonth = now()->format('Y-m');
        $latestRIS = RIS::where('region_id', $request->region_id)
            ->whereYear('ris_date', now()->year)
            ->whereMonth('ris_date', now()->month)
            ->latest('ris_id')
            ->first();

        $lastNumber = $latestRIS
            ? (int) filter_var(substr($latestRIS->ris_number, -4), FILTER_SANITIZE_NUMBER_INT)
            : 0;

        $nextNumber = $lastNumber + 1;
        $risNumber = $yearMonth . '-' . $regionCode . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // 4️⃣ Create RIS
        $ris = RIS::create([
            'ris_number' => $risNumber,
            'responsibility_center' => $request->responsibility_center,
            'region_id' => $request->region_id,
            'office_id' => $request->office_id,
            'fund_cluster' => $request->fund_cluster,
            'ris_date' => $request->ris_date,
            'purpose' => $request->purpose,
            'requested_by_id' => $request->requested_by_id,
            'received_by_id' => $request->received_by_id,
            'approved_by_id' => $request->approved_by_id,
            'status' => 'pending',
        ]);

        // 5️⃣ Prepare RIS items
        $itemsData = collect($request->items)->map(function ($item) use ($ris) {
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
        })->toArray();

        // 6️⃣ Bulk insert RIS items
        RISItem::insert($itemsData);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'RIS successfully created.',
            'data' => $ris->load('items')
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();

        \Log::error('RIS Store Error: ' . $e->getMessage(), [
            'request' => $request->all(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to create RIS.',
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'request_payload' => $request->all()
        ], 500);
    }
}
}

