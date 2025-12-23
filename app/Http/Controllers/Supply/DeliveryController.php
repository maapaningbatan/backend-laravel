<?php

namespace App\Http\Controllers\Supply;

use App\Http\Controllers\Controller;
use App\Models\Supply\Delivery;
use App\Models\Supply\ItemDelivery;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Property\PropertyCard;
use App\Models\Property\SemiExCard;
use App\Models\Supply\StockCard;

class DeliveryController extends Controller
{
    // 📌 List all deliveries with items and related info
    public function index()
    {
        $deliveries = Delivery::with(['items', 'supplierInfo', 'preparedByUser'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json($deliveries);
    }

    // 📌 Get next delivery code
    public function getNextCode()
    {
        $last = Delivery::orderByDesc('code_number')->first();
        $nextNumber = $last && $last->code_number ? (int) $last->code_number + 1 : 1;
        $nextCode = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return response()->json(['nextCode' => $nextCode]);
    }

    // 📌 Store a new delivery with items
    public function store(Request $request)
    {
        $validated = $request->validate([
            'iar_number' => 'required|string',
            'supplier_id' => 'nullable|integer',
            'purchase_order_number' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'pr_number' => 'nullable|string',
            'pr_date' => 'nullable|date',
            'warehouse_id' => 'nullable|integer',
            'receiving_office' => 'nullable|string',
            'invoice_no' => 'nullable|string',
            'invoice_total_amount' => 'nullable|numeric',
            'invoice_date' => 'nullable|date',
            'po_amount' => 'nullable|numeric',
            'po_date' => 'nullable|date',
            'dr_no' => 'nullable|string',
            'dr_date' => 'nullable|date',
            'ris_no' => 'nullable|string',
            'ris_date' => 'nullable|date',
            'ors_no' => 'nullable|string',
            'ors_date' => 'nullable|date',
            'dv_no' => 'nullable|string',
            'dv_date' => 'nullable|date',
            'code_number' => 'nullable|string',
            'purpose' => 'nullable|string',
            'items' => 'required|array',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $delivery = Delivery::create([
                'iar_number' => $validated['iar_number'] ?? null,
                'supplier_id' => $validated['supplier_id'] ?? null,
                'purchase_order_number' => $validated['purchase_order_number'] ?? null,
                'purchase_date' => $validated['purchase_date'] ? Carbon::parse($validated['purchase_date']) : null,
                'pr_number' => $validated['pr_number'] ?? null,
                'pr_date' => $validated['pr_date'] ? Carbon::parse($validated['pr_date']) : null,
                'warehouse_id' => $validated['warehouse_id'] ?? null,
                'receiving_office' => $validated['receiving_office'] ?? 'AS-PMD',
                'purpose' => $validated['purpose'] ?? null,
                'invoice_no' => $validated['invoice_no'] ?? null,
                'invoice_total_amount' => $validated['invoice_total_amount'] ?? 0,
                'invoice_date' => $validated['invoice_date'] ? Carbon::parse($validated['invoice_date']) : null,
                'po_amount' => $validated['po_amount'] ?? 0,
                'po_date' => $validated['po_date'] ? Carbon::parse($validated['po_date']) : null,
                'dr_no' => $validated['dr_no'] ?? null,
                'dr_date' => $validated['dr_date'] ? Carbon::parse($validated['dr_date']) : null,
                'ris_no' => $validated['ris_no'] ?? null,
                'ris_date' => $validated['ris_date'] ? Carbon::parse($validated['ris_date']) : null,
                'ors_no' => $validated['ors_no'] ?? null,
                'ors_date' => $validated['ors_date'] ? Carbon::parse($validated['ors_date']) : null,
                'dv_no' => $validated['dv_no'] ?? null,
                'dv_date' => $validated['dv_date'] ? Carbon::parse($validated['dv_date']) : null,
                'code_number' => $validated['code_number'] ?? null,
                'prepared_by' => $request->user()->id ?? null,
                'status' => 'Pending',
            ]);

            // Save delivery items
            foreach ($validated['items'] as $item) {
                $delivery->items()->create([
                    'supply_id' => $item['supply_id'] ?? null,
                    'item_type' => $item['item_type'] ?? null,
                    'stock_number' => $item['stock_number'] ?? null,
                    'unit_id' => $item['unit_id'] ?? null,
                    'category_id' => $item['category_id'] ?? null,
                    'quantity' => $item['quantity'] ?? 0,
                    'unit_value' => $item['unit_value'] ?? 0,
                    'total_amount' => $item['total_amount'] ?? 0,
                    'brand_id' => $item['brand_id'] ?? null,
                    'model_id' => $item['model_id'] ?? null,
                    'additional_description' => $item['additional_description'] ?? null,
                    'remarks' => $item['remarks'] ?? null,
                ]);
            }

            return response()->json([
                'message' => 'Delivery saved successfully!',
                'data' => $delivery->load('items', 'supplierInfo', 'preparedByUser'),
            ], 201);
        });
    }

    // 📌 Show a single delivery
public function show($id)
{
    $delivery = Delivery::with([
        'supplierInfo',
        'warehouse',
        'preparedByUser',
        'items' => function($query) {
            $query->select(
                'id',
                'delivery_id',
                'supply_id',
                'item_type',
                'stock_number',
                'unit_id',
                'category_id',
                'brand_id',
                'model_id',
                'additional_description',
                'remarks',
                'quantity',
                'unit_value',
                'total_amount'
            )->with([
                'supply:id,supplies_desc',   // optional, to show supply name
                'unit:id,unit_code',
                'category:id,category_desc',
                'brand:id,brand_desc',
                'model:id,model_desc',
                'itemType:id,itemtype_desc'
            ]);
        }
    ])->findOrFail($id);

    return response()->json([
        'data' => $delivery
    ]);
}

    // 📌 Update delivery and its items
public function update(Request $request, int $id)
{
    $validated = $request->validate([
        'iar_number' => 'required|string',
        'supplier_id' => 'nullable|integer',
        'purchase_order_number' => 'nullable|string',
        'purchase_date' => 'nullable|date',
        'pr_number' => 'nullable|string',
        'pr_date' => 'nullable|date',
        'warehouse_id' => 'nullable|integer',
        'receiving_office' => 'nullable|string',
        'purpose' => 'nullable|string',
        'invoice_no' => 'nullable|string',
        'invoice_total_amount' => 'nullable|numeric',
        'invoice_date' => 'nullable|date',
        'po_amount' => 'nullable|numeric',
        'po_date' => 'nullable|date',
        'dr_no' => 'nullable|string',
        'dr_date' => 'nullable|date',
        'ris_no' => 'nullable|string',
        'ris_date' => 'nullable|date',
        'ors_no' => 'nullable|string',
        'ors_date' => 'nullable|date',
        'dv_no' => 'nullable|string',
        'dv_date' => 'nullable|date',
        'code_number' => 'nullable|string',
        'prepared_by' => 'nullable|integer',
        'items' => 'required|array',
    ]);

    return DB::transaction(function () use ($validated, $id, $request) {
        $delivery = Delivery::findOrFail($id);

        // 🚫 Prevent editing if already approved
        if ($delivery->status === 'Approved') {
            return response()->json([
                'message' => 'This delivery has already been approved and cannot be edited.',
            ], 403);
        }

        // ✅ Update delivery main fields
        $delivery->update([
            'iar_number' => $validated['iar_number'],
            'supplier_id' => $validated['supplier_id'] ?? null,
            'purchase_order_number' => $validated['purchase_order_number'] ?? null,
            'purchase_date' => $validated['purchase_date'] ?? null,
            'pr_number' => $validated['pr_number'] ?? null,
            'pr_date' => $validated['pr_date'] ?? null,
            'warehouse_id' => $validated['warehouse_id'] ?? null,
            'receiving_office' => $validated['receiving_office'] ?? $delivery->receiving_office,
            'purpose' => $validated['purpose'] ?? null,
            'invoice_no' => $validated['invoice_no'] ?? null,
            'invoice_total_amount' => $validated['invoice_total_amount'] ?? 0,
            'invoice_date' => $validated['invoice_date'] ?? null,
            'po_amount' => $validated['po_amount'] ?? 0,
            'po_date' => $validated['po_date'] ?? null,
            'dr_no' => $validated['dr_no'] ?? null,
            'dr_date' => $validated['dr_date'] ?? null,
            'ris_no' => $validated['ris_no'] ?? null,
            'ris_date' => $validated['ris_date'] ?? null,
            'ors_no' => $validated['ors_no'] ?? null,
            'ors_date' => $validated['ors_date'] ?? null,
            'dv_no' => $validated['dv_no'] ?? null,
            'dv_date' => $validated['dv_date'] ?? null,
            'code_number' => $validated['code_number'] ?? $delivery->code_number,
            'prepared_by' => $validated['prepared_by'] ?? $delivery->prepared_by,
            'updated_by' => $request->user()->id ?? null,
        ]);

        // 🔄 Sync item deliveries
        $existingIds = $delivery->items()->pluck('id')->toArray();
        $incomingIds = array_filter(array_column($validated['items'], 'item_delivery_id'));

        // Delete removed items
        $toDelete = array_diff($existingIds, $incomingIds);
        if (!empty($toDelete)) {
            $delivery->items()->whereIn('id', $toDelete)->delete();
        }

        // Insert or update each item
        foreach ($validated['items'] as $item) {
            $payload = [
                'supply_id' => $item['supply_id'] ?? null,
                'item_type' => $item['item_type'] ?? null,
                'stock_number' => $item['stock_number'] ?? null,
                'unit_id' => $item['unit_id'] ?? null,
                'category_id' => $item['category_id'] ?? null,
                'brand_id' => $item['brand_id'] ?? null,
                'model_id' => $item['model_id'] ?? null,
                'additional_description' => $item['additional_description'] ?? null,
                'remarks' => $item['remarks'] ?? null,
                'quantity' => $item['quantity'] ?? 0,
                'unit_value' => $item['unit_value'] ?? 0,
                'total_amount' => $item['total_amount'] ?? 0,
            ];

            if (!empty($item['item_delivery_id'])) {
                // 🟢 Update existing item
                $delivery->items()->where('id', $item['item_delivery_id'])->update($payload);
            } else {
                // 🟢 Create new item (ensure delivery_id attached)
                $delivery->items()->create([
                    ...$payload,
                    'delivery_id' => $delivery->id,
                ]);
            }
        }

        return response()->json([
            'message' => 'Delivery updated successfully!',
            'data' => $delivery->load([
                'items.unit',
                'items.brand',
                'items.model',
                'supplierInfo',
                'preparedByUser',
            ]),
        ], 200);
    });
}



public function approve($id)
{
    return DB::transaction(function () use ($id) {
        $delivery = Delivery::with('items')->findOrFail($id);

        if ($delivery->status === 'Approved') {
            return response()->json(['message' => 'Delivery already approved.'], 400);
        }

        $itemTypes = DB::table('lib_itemtype')->pluck('itemtype_desc', 'id');

        foreach ($delivery->items as $item) {
            $itemTypeName = strtolower(trim($itemTypes[$item->item_type] ?? ''));

            // Determine destination model based on item type
            switch ($itemTypeName) {
                case 'ppe':
                    $model = PropertyCard::class;
                    break;
                case 'semi-expendable':
                    $model = SemiExCard::class;
                    break;
                case 'semi-expendable supplies (non-consumable)':
                case 'consumable supplies':
                    $model = StockCard::class;
                    break;
                default:
                    continue 2; // Skip if unknown type
            }

            // Get last balance for this supply
            $lastBalance = $model::where('supply_id', $item->supply_id)
                ->orderByDesc('transaction_date')
                ->orderByDesc('id')
                ->value('balance');

            $newBalance = ($lastBalance ?? 0) + $item->quantity;

            // Create new card record
            $recordData = [
                'delivery_id' => $delivery->id,
                'item_delivery_id' => $item->id,
                'supply_id' => $item->supply_id,
                'transaction_date' => $delivery->purchase_date ?? now(),
                'entry_type' => 'Delivery',
                'reference_no' => $delivery->purchase_order_number,
                'receipt_qty' => $item->quantity,
                'remaining_balance' =>  $item->quantity,
                'balance' => $newBalance,
                'unit_value' => $item->unit_value,
                'unit' => $item->unit_id,
                'total' => $item->total_amount,
                'remarks' => $item->remarks,
                'supplier_id' => $delivery->supplier_id,
                'sale_invoice' => $delivery->invoice_no,
                'item_type_id' => $item->item_type,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Only PropertyCard and SemiExCard have item_delivery_id
            if (in_array($model, [PropertyCard::class, SemiExCard::class])) {
                $recordData['item_delivery_id'] = $item->id;
            }

            // Save record using Eloquent
            $model::create($recordData);
        }

        // Update delivery status
        $delivery->update([
            'status' => 'Approved',
            'approved_at' => now(),
        ]);

        return response()->json([
            'message' => 'Delivery approved and items successfully recorded.',
            'data' => $delivery->load('items'),
        ]);
    });
}




    // 📌 Delete a delivery
    public function destroy($id)
    {
        $delivery = Delivery::findOrFail($id);

        if ($delivery->status === 'Approved') {
            return response()->json(['message' => 'Approved deliveries cannot be deleted.'], 403);
        }

        $delivery->delete();

        return response()->json(['message' => 'Delivery deleted successfully.']);
    }
}
