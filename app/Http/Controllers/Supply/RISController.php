<?php

namespace App\Http\Controllers\Supply;

use App\Http\Controllers\Controller;
use App\Models\Supply\Ris;
use App\Models\Supply\RisItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RISController extends Controller
{
    /**
     * Display a listing of RIS.
     */
    public function index()
    {
        try {
            $ris = Ris::with([
                'region',
                'office',
                'division',
                'fundCluster',
                'requestedBy.position',
                'receivedBy.position',
                'approvedBy.employee.position',
                'items.supply.unit',
                'items.unit',
            ])
                ->orderByDesc('created_at')
                ->get();

            return response()->json($ris);
        } catch (\Throwable $e) {
            Log::error('RIS Index Error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch RIS.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created RIS in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'responsibility_center' => 'nullable|string',
            'fund_cluster_id' => 'required|integer|exists:lib_funds,id',
            'ris_date' => 'required|date',
            'purpose' => 'required|string',
            'requested_by_id' => 'required|integer|exists:lib_employees,id',
            'received_by_id' => 'required|integer|exists:lib_employees,id',
            'approved_by_id' => 'required|integer|exists:lib_property_chiefs,id',
            'items' => 'required|array|min:1',
            'items.*.supply_id' => 'required|integer|exists:lib_supplies,id',
            'items.*.unit_id' => 'required|integer|exists:lib_units,id',
            'items.*.quantity_requested' => 'required|integer|min:1',
            'items.*.description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $user = auth()->user();
            $region = DB::table('lib_regions')->where('id', $user->region_id)->first();
            $regionCode = strtoupper($region->region_code ?? 'XX');
            $yearMonth = now()->format('Y-m');

            // Generate unique RIS number per region and month
            $lastNumber = DB::table('tbl_ris')
                ->where('region_id', $user->region_id)
                ->where('ris_number', 'like', "$regionCode-$yearMonth-%")
                ->max(DB::raw('CAST(SUBSTRING(ris_number, -4) AS UNSIGNED)'));

            $nextNumber = $lastNumber ? $lastNumber + 1 : 1;
            $risNumber = sprintf('%s-%s-%04d', $regionCode, $yearMonth, $nextNumber);

            // Create RIS record
            $ris = Ris::create([
                'ris_number' => $risNumber,
                'responsibility_center' => $request->responsibility_center,
                'region_id' => $user->region_id,
                'office_id' => $user->office_id,
                'division_id' => $user->division_id,
                'fund_cluster_id' => $request->fund_cluster_id,
                'ris_date' => $request->ris_date,
                'purpose' => $request->purpose,
                'requested_by_id' => $request->requested_by_id,
                'received_by_id' => $request->received_by_id,
                'approved_by_id' => $request->approved_by_id,
                'status' => 'pending',
                'created_by' => $user->id,
            ]);

            // Insert RIS items
            $itemsData = collect($request->items)->map(fn ($item) => [
                'ris_id' => $ris->id,
                'supply_id' => $item['supply_id'],
                'unit_id' => $item['unit_id'],
                'quantity_requested' => $item['quantity_requested'],
                'quantity_issued' => $item['quantity_issued'] ?? null,
                'description' => $item['description'] ?? null,
                'remarks' => $item['remarks'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray();

            RisItem::insert($itemsData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'RIS successfully created.',
                'data' => $ris->load('items.supply.unit', 'items.unit', 'createdBy'),
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('RIS Store Error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'request' => $request->all()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create RIS.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified RIS.
     */
    public function show($id)
    {
        try {
            $ris = Ris::with([
                'region',
                'office',
                'division',
                'fundCluster',
                'requestedBy.position',
                'receivedBy.position',
                'approvedBy.employee.position',
                'items.supply.unit',
                'items.unit',
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $ris,
            ]);
        } catch (\Throwable $e) {
            Log::error('RIS Show Error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch RIS.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified RIS in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'responsibility_center' => 'nullable|string',
            'fund_cluster_id' => 'required|integer|exists:lib_funds,id',
            'ris_date' => 'required|date',
            'purpose' => 'required|string',
            'requested_by_id' => 'required|integer|exists:lib_employees,id',
            'received_by_id' => 'required|integer|exists:lib_employees,id',
            'approved_by_id' => 'required|integer|exists:lib_property_chiefs,id',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|integer|exists:tbl_ris_items,id',
            'items.*.supply_id' => 'required|integer|exists:lib_supplies,id',
            'items.*.unit_id' => 'required|integer|exists:lib_units,id',
            'items.*.quantity_requested' => 'required|integer|min:1',
            'items.*.description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $user = auth()->user();
            $ris = Ris::findOrFail($id);

            $ris->update([
                'responsibility_center' => $request->responsibility_center,
                'region_id' => $user->region_id,
                'office_id' => $user->office_id,
                'division_id' => $user->division_id,
                'fund_cluster_id' => $request->fund_cluster_id,
                'ris_date' => $request->ris_date,
                'purpose' => $request->purpose,
                'requested_by_id' => $request->requested_by_id,
                'received_by_id' => $request->received_by_id,
                'approved_by_id' => $request->approved_by_id,
            ]);

            // Manage RIS items
            $existingIds = $ris->items()->pluck('id')->toArray();
            $incomingIds = collect($request->items)->pluck('id')->filter()->toArray();

            // Delete removed items
            $toDelete = array_diff($existingIds, $incomingIds);
            if ($toDelete) {
                RisItem::whereIn('id', $toDelete)->delete();
            }

            // Upsert items
            foreach ($request->items as $item) {
                if (! empty($item['id'])) {
                    RisItem::where('id', $item['id'])->update([
                        'supply_id' => $item['supply_id'],
                        'unit_id' => $item['unit_id'],
                        'quantity_requested' => $item['quantity_requested'],
                        'quantity_issued' => $item['quantity_issued'] ?? null,
                        'description' => $item['description'] ?? null,
                        'remarks' => $item['remarks'] ?? null,
                    ]);
                } else {
                    RisItem::create([
                        'ris_id' => $ris->id,
                        'supply_id' => $item['supply_id'],
                        'unit_id' => $item['unit_id'],
                        'quantity_requested' => $item['quantity_requested'],
                        'quantity_issued' => $item['quantity_issued'] ?? null,
                        'description' => $item['description'] ?? null,
                        'remarks' => $item['remarks'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'RIS updated successfully.',
                'data' => $ris->load('items.supply.unit', 'items.unit'),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('RIS Update Error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'request' => $request->all()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update RIS.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Fetch RIS for printing.
     */
    public function print($id)
    {
        try {
            $ris = Ris::with([
                'region',
                'office',
                'division',
                'receivedBy.position',
                'requestedBy.position',
                'approvedBy.employee.position',
                'items.supply.unit',
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $ris,
            ]);
        } catch (\Throwable $e) {
            Log::error('RIS Print Error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch RIS for printing.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
