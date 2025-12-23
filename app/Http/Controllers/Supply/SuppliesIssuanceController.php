<?php

namespace App\Http\Controllers\Supply;

use App\Http\Controllers\Controller;
use App\Models\Library\LibEmployee;
use App\Models\Supply\StockCard;
use App\Models\Supply\SuppliesIssuance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SuppliesIssuanceController extends Controller
{
    /**
     * List all supply issuance records.
     */
    public function index()
    {
        try {
            $issuances = SuppliesIssuance::where('isdeleted', 0)
                ->orderBy('date_created', 'desc')
                ->with([
                    'office:id,office_desc',
                    'division:id,division_desc',
                    'issuer.employee:id,first_name,middle_name,last_name,honorific,suffix,title',
                    'approver.employee:id,first_name,middle_name,last_name,honorific,suffix,title',
                    'creator:id,username',
                ])
                ->get()
                ->map(function ($i) {
                    return [
                        'id' => $i->id,
                        'ris_number' => $i->ris_number,
                        'pr_number' => $i->pr_number,
                        'date_issued' => $i->date_issued,
                        'accountable_office' => $i->office?->office_desc ?? 'N/A',
                        'accountable_division' => $i->division?->division_desc ?? 'N/A',
                        'received_by' => $i->receiver?->full_name ?? 'N/A',
                        'issued_by' => $i->issuer?->employee?->full_name ?? 'N/A',
                        'approved_by' => $i ->approver?->full_name ?? 'N/A',
                        'prepared_by' => $i->prepared_by,
                        'remarks' => $i->remarks,
                        'created_by' => $i->creator?->username ?? 'N/A',
                        'date_created' => $i->date_created,
                    ];
                });

            return response()->json($issuances);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to load supply issuance records.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store new supply issuance records.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date_issued' => 'required|date',
            'issued_by' => 'required|integer',
            'approved_by' => 'required|integer',
            'remarks' => 'nullable|string',
            'ris_no' => 'required|string|max:100',
            'accountable_office' => 'required|integer',
            'accountable_division' => 'required|integer',
            'items' => 'required|array|min:1',

            // ITEM FIELDS
            'items.*.supply_id' => 'required|integer',
            'items.*.quantity_requested' => 'required|numeric|min:1',
            'items.*.stock_card_id' => 'required|integer',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.unit_id' => 'nullable|integer',
            'items.*.item_type_id' => 'nullable|integer',
            'items.*.received_by' => 'required|integer',
            'items.*.supplier_id' => 'required|integer',
            'items.*.sale_invoice' => 'required|string|max:100',
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();

            // Office / Division from request
            $office = $validated['accountable_office'];
            $division = $validated['accountable_division'];

            // --------------------------------------------------------------
            // CREATE SUPPLIES ISSUANCE (NO RECEIVED_BY HERE)
            // --------------------------------------------------------------
            $issuance = SuppliesIssuance::create([
                'ris_number' => $validated['ris_no'],
                'date_issued' => $validated['date_issued'],
                'region_id' => $user->region_id,
                'accountable_office' => $office,
                'accountable_division' => $division,
                'issued_by' => $validated['issued_by'],
                'approved_by' => $validated['approved_by'],
                'prepared_by' => $user->id,
                'remarks' => $validated['remarks'] ?? null,
                'created_by' => $user->id,
                'date_created' => now(),
            ]);

            // --------------------------------------------------------------
            // LOOP ITEMS AND ISSUE STOCK (receiver is PER ITEM)
            // --------------------------------------------------------------
            foreach ($validated['items'] as $item) {

                // Receiver per item
                $receiverModel = LibEmployee::with(['office', 'division'])
                    ->findOrFail($item['received_by']);

                $receiverName = $receiverModel->full_name;

                // Issue stock
                $this->issueStock(
                    $item['supply_id'],
                    $office,
                    $division,
                    $item['quantity_requested'],
                    $validated['ris_no'],
                    $validated['date_issued'],
                    $issuance->id,
                    $item['stock_card_id'],
                    $item['unit_cost'],
                    $item['amount'],
                    $item['item_type_id'] ?? null,
                    $item['received_by'],
                    $item['unit_id'] ?? null,
                    $item['supplier_id'] ?? null,
                    $item['sale_invoice'] ?? null

                );
            }

            DB::commit();

            return response()->json([
                'message' => 'Supplies issuance created successfully.',
                'data' => $issuance,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create issuance.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function issueStock(
        $supplyId,
        $officeId,
        $divisionId,
        $qty,
        $risNumber,
        $dateIssued,
        $issuanceId,
        $sourceStockCardId,
        $unitValue,
        $total,
        $itemTypeId = null,
        $receiverId = null,
        $unitId = null,
        $supplierId = null,
        $saleInvoice = null


    ) {
        $stock = StockCard::lockForUpdate()->findOrFail($sourceStockCardId);

        if ($stock->remaining_balance < $qty) {
            throw new \Exception("Not enough stock in selected entry. Available: {$stock->remaining_balance}");
        }

        // Latest balance of supply
        $latestBalance = StockCard::where('supply_id', $supplyId)
            ->orderBy('id', 'desc')
            ->lockForUpdate()
            ->value('balance') ?? 0;

        $newBalance = $latestBalance - $qty;

        // Generate property_no if semi-expendable (item_type = 3)
        $propertyNo = null;
        $icsNo = null;

        if ($itemTypeId == 3) {
            $loggedInUser = Auth::user()->load('region');
            $regionCode = $loggedInUser->region?->region_code ?? 'XX';
            $year = date('Y', strtotime($dateIssued));
            $month = date('m', strtotime($dateIssued));

            // Prefix low/high value
            $prefix = ($unitValue < 5000) ? 'S-SPLV' : 'S-SPHV';

            // last property_no
            $lastPropertyNo = StockCard::where('property_no', 'like', "$regionCode-$prefix-$year-$month-%")
                ->where('item_type_id', 3)
                ->orderBy('property_no', 'desc')
                ->lockForUpdate()
                ->value('property_no');

            $sequence = $lastPropertyNo ? intval(substr($lastPropertyNo, -4)) + 1 : 1;

            $propertyNo = sprintf('%s-%s-%s-%s-%04d', $regionCode, $prefix, $year, $month, $sequence);

            // ICS number
            $lastIcsNo = StockCard::where('ics_no', 'like', "$regionCode-S-NI-$year-$month-%")
                ->orderBy('ics_no', 'desc')
                ->lockForUpdate()
                ->value('ics_no');

            $sequenceIcs = $lastIcsNo ? intval(substr($lastIcsNo, -4)) + 1 : 1;

            $icsNo = sprintf('%s-%s-%s-%s-%s-%04d', $regionCode, 'S', 'NI', $year, $month, $sequenceIcs);
        }

        // Create new stock card entry
        StockCard::create([
            'supply_id' => $supplyId,
            'transaction_date' => $dateIssued,
            'reference_no' => $risNumber,
            'issued_qty' => $qty,
            'unit_value' => $unitValue,
            'unit' => $unitId,
            'total' => $total,
            'office_id' => $officeId,
            'division_id' => $divisionId,
            'received_by' => $receiverId,
            'balance' => $newBalance,
            'remaining_balance' => 0,
            'supplies_issuance_id' => $issuanceId,
            'entry_type' => 'Issuance',
            'fifo_source_id' => $sourceStockCardId,
            'item_type_id' => $itemTypeId,
            'property_no' => $propertyNo,
            'ics_no' => $icsNo,
            'supplier_id' => $supplierId,
            'sale_invoice' => $saleInvoice,

        ]);

        // Deduct from original stock
        $stock->remaining_balance -= $qty;
        $stock->save();
    }

    public function show($id)
    {
        $issuance = SuppliesIssuance::with(['items'])->findOrFail($id);

        // Optionally, map the items to include necessary fields
        $issuance->items = $issuance->items->map(function ($i) {
            return [
                'supply_id' => $i->supply_id,
                'supply_name' => $i->supply->supplies_desc ?? 'Unknown',
                'stock_number' => $i->stock_card_id,
                'quantity_requested' => $i->issued_qty,
                'unit_id' => $i->unit,
                'unit' => $i->unitRelation?->unit_code ?? '-',
                'unit_cost' => $i->unit_value,
                'amount' => $i->total,
                'po_no' => $i->po_no ?? null,
                'item_type_id' => $i->item_type_id,
                'itemtype_name' => $i->itemtype?->itemtype_desc ?? '-',
                'remarks' => $i->remarks,
                'receiver' => $i->receiver ?? '',
                'supplier_name' => $i->supplier?->supplier_name ?? '-',
                'supplier_id' => $i->supplier_id,
                'property_no' => $i->property_no,
                'ics_no' => $i->ics_no,
            ];
        });

        return response()->json($issuance);
    }

    /**
     * Update an issuance record.
     */
    public function update(Request $request, $id)
    {
        $issuance = SuppliesIssuance::findOrFail($id);

        $validated = $request->validate([
            'ris_number' => 'required|string|max:100',
            'date_issued' => 'required|date',
            'region_id' => 'nullable|integer',
            'accountable_office' => 'required|integer',
            'accountable_division' => 'nullable|integer',
            'issued_by' => 'nullable|integer',
            'approved_by' => 'nullable|integer',
            'prepared_by' => 'nullable|integer',
            'remarks' => 'nullable|string',
        ]);

        $validated['updated_by'] = Auth::id() ?? 1;
        $validated['date_updated'] = now();

        $issuance->update($validated);

        return response()->json([
            'message' => 'Supplies issuance record updated successfully.',
            'data' => $issuance,
        ]);
    }

    /**
     * Soft delete an issuance record.
     */
    public function destroy($id)
    {
        $issuance = SuppliesIssuance::findOrFail($id);

        $issuance->update([
            'isdeleted' => 1,
            'deleted_by' => Auth::id() ?? 1,
            'date_deleted' => now(),
        ]);

        return response()->json(['message' => 'Supplies issuance record deleted successfully.']);
    }
}
