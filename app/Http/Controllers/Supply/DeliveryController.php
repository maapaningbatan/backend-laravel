<?php

namespace App\Http\Controllers\Supply;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Supply\Delivery;

class DeliveryController extends Controller
{
    // 📌 List all deliveries
    public function index()
    {
        $deliveries = Delivery::with(['supplierInfo', 'preparedByEmployee'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($deliveries);
    }

    // 📌 Get next delivery code
    public function getNextCode()
    {
        $last = DB::table('tbl_delivery')->orderByDesc('code_number')->first();
        $nextNumber = $last && $last->code_number ? (int) $last->code_number + 1 : 0;
        $nextCode = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return response()->json(['nextCode' => $nextCode]);
    }

    // 📌 Store a new delivery with items
    public function store(Request $request)
    {
        $validated = $request->validate([
            'iar_number' => 'nullable|string',
            'supplier' => 'nullable|string',
            'purchase_order_number' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'pr_number' => 'nullable|string',
            'pr_date' => 'nullable|date',
            'warehouse' => 'nullable|string',
            'receiving_office' => 'nullable|string',
            'invoice_no' => 'nullable|string',
            'invoice_total_amount' => 'nullable|numeric',
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

        return DB::transaction(function () use ($validated) {
            $delivery = Delivery::create([
                'iar_number' => $validated['iar_number'] ?? null,
                'supplier' => $validated['supplier'] ?? null,
                'purchase_order_number' => $validated['purchase_order_number'] ?? null,
                'purchase_date' => isset($validated['purchase_date']) ? Carbon::parse($validated['purchase_date']) : null,
                'pr_number' => $validated['pr_number'] ?? null,
                'pr_date' => isset($validated['pr_date']) ? Carbon::parse($validated['pr_date']) : null,
                'receiving_office' => $validated['receiving_office'] ?? 'AS-PMD',
                'warehouse' => $validated['warehouse'] ?? null,
                'purpose' => $validated['purpose'] ?? null,
                'invoice_no' => $validated['invoice_no'] ?? null,
                'invoice_total_amount' => $validated['invoice_total_amount'] ?? 0,
                'po_amount' => $validated['po_amount'] ?? 0,
                'po_date' => isset($validated['po_date']) ? Carbon::parse($validated['po_date']) : null,
                'dr_no' => $validated['dr_no'] ?? null,
                'dr_date' => isset($validated['dr_date']) ? Carbon::parse($validated['dr_date']) : null,
                'ris_no' => $validated['ris_no'] ?? null,
                'ris_date' => isset($validated['ris_date']) ? Carbon::parse($validated['ris_date']) : null,
                'ors_no' => $validated['ors_no'] ?? null,
                'ors_date' => isset($validated['ors_date']) ? Carbon::parse($validated['ors_date']) : null,
                'dv_no' => $validated['dv_no'] ?? null,
                'dv_date' => isset($validated['dv_date']) ? Carbon::parse($validated['dv_date']) : null,
                'code_number' => $validated['code_number'] ?? null,
                'prepared_by' => Auth::user()->employee_pk,
                'status' => 'Pending', // default new delivery is Pending
            ]);

            foreach ($validated['items'] as $item) {
                $delivery->items()->create([
                    'supply' => $item['supply'] ?? null,
                    'item_type' => $item['item_type'] ?? 'Supplies (Consumable)',
                    'stock_number' => $item['stock_number'] ?? null,
                    'unit' => $item['unit'] ?? null,
                    'category' => $item['category'] ?? null,
                    'quantity' => $item['quantity'] ?? 0,
                    'unit_value' => $item['unit_value'] ?? 0,
                    'total_amount' => $item['total_amount'] ?? 0,
                    'brand' => $item['brand'] ?? null,
                    'model' => $item['model'] ?? null,
                    'additional_description' => $item['additional_description'] ?? null,
                    'remarks' => $item['remarks'] ?? null,
                ]);
            }

            return response()->json([
                'message' => 'Delivery saved successfully!',
                'data' => $delivery->load('items', 'preparedByEmployee', 'supplierInfo')
            ], 201);
        });
    }

    // 📌 Show a single delivery with items
    public function show($id)
    {
        $delivery = Delivery::with(['supplierInfo', 'preparedByEmployee', 'items'])->find($id);

        if (!$delivery) {
            return response()->json(['message' => 'Delivery not found'], 404);
        }

        return response()->json($delivery);
    }

    // 📌 Update delivery and items
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'iar_number' => 'nullable|string',
            'supplier' => 'nullable|string',
            'purchase_order_number' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'pr_number' => 'nullable|string',
            'pr_date' => 'nullable|date',
            'warehouse' => 'nullable|string',
            'receiving_office' => 'nullable|string',
            'invoice_no' => 'nullable|string',
            'invoice_total_amount' => 'nullable|numeric',
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

        return DB::transaction(function () use ($validated, $id) {
            $delivery = Delivery::findOrFail($id);

            // 🚫 Approved → locked
            if ($delivery->status === 'Approved') {
                return response()->json([
                    'message' => 'This delivery has already been approved and cannot be edited.'
                ], 403);
            }

            // ✅ Pending → editable
            $delivery->update([
                'iar_number' => $validated['iar_number'] ?? null,
                'supplier' => $validated['supplier'] ?? null,
                'purchase_order_number' => $validated['purchase_order_number'] ?? null,
                'purchase_date' => isset($validated['purchase_date']) ? Carbon::parse($validated['purchase_date']) : null,
                'pr_number' => $validated['pr_number'] ?? null,
                'pr_date' => isset($validated['pr_date']) ? Carbon::parse($validated['pr_date']) : null,
                'receiving_office' => $validated['receiving_office'] ?? $delivery->receiving_office,
                'warehouse' => $validated['warehouse'] ?? null,
                'purpose' => $validated['purpose'] ?? null,
                'invoice_no' => $validated['invoice_no'] ?? null,
                'invoice_total_amount' => $validated['invoice_total_amount'] ?? 0,
                'po_amount' => $validated['po_amount'] ?? 0,
                'po_date' => isset($validated['po_date']) ? Carbon::parse($validated['po_date']) : null,
                'dr_no' => $validated['dr_no'] ?? null,
                'dr_date' => isset($validated['dr_date']) ? Carbon::parse($validated['dr_date']) : null,
                'ris_no' => $validated['ris_no'] ?? null,
                'ris_date' => isset($validated['ris_date']) ? Carbon::parse($validated['ris_date']) : null,
                'ors_no' => $validated['ors_no'] ?? null,
                'ors_date' => isset($validated['ors_date']) ? Carbon::parse($validated['ors_date']) : null,
                'dv_no' => $validated['dv_no'] ?? null,
                'dv_date' => isset($validated['dv_date']) ? Carbon::parse($validated['dv_date']) : null,
                'code_number' => $validated['code_number'] ?? $delivery->code_number,
                'updated_by' => Auth::id(),
            ]);

            // 🔄 Items sync
$existingItemIds = $delivery->items()->pluck('item_delivery_id')->toArray();
$incomingItemIds = array_filter(array_column($validated['items'], 'item_delivery_id'));

$itemsToDelete = array_diff($existingItemIds, $incomingItemIds);
if (!empty($itemsToDelete)) {
    $delivery->items()->whereIn('item_delivery_id', $itemsToDelete)->delete();
}

foreach ($validated['items'] as $item) {
    if (!empty($item['item_delivery_id'])) {
        $delivery->items()->where('item_delivery_id', $item['item_delivery_id'])->update([
            'supply' => $item['supply'] ?? null,
            'item_type' => $item['item_type'] ?? 'Supplies (Consumable)',
            'stock_number' => $item['stock_number'] ?? null,
            'unit' => $item['unit'] ?? null,
            'category' => $item['category'] ?? null,
            'quantity' => $item['quantity'] ?? 0,
            'unit_value' => $item['unit_value'] ?? 0,
            'total_amount' => $item['total_amount'] ?? 0,
            'brand' => $item['brand'] ?? null,
            'model' => $item['model'] ?? null,
            'additional_description' => $item['additional_description'] ?? null,
            'remarks' => $item['remarks'] ?? null,
            'updated_at' => now(),
        ]);
    } else {
        $delivery->items()->create([
            'supply' => $item['supply'] ?? null,
            'item_type' => $item['item_type'] ?? 'Supplies (Consumable)',
            'stock_number' => $item['stock_number'] ?? null,
            'unit' => $item['unit'] ?? null,
            'category' => $item['category'] ?? null,
            'quantity' => $item['quantity'] ?? 0,
            'unit_value' => $item['unit_value'] ?? 0,
            'total_amount' => $item['total_amount'] ?? 0,
            'brand' => $item['brand'] ?? null,
            'model' => $item['model'] ?? null,
            'additional_description' => $item['additional_description'] ?? null,
            'remarks' => $item['remarks'] ?? null,
        ]);
    }
}

            return response()->json([
                'message' => 'Delivery updated successfully!',
                'data' => $delivery->load('items', 'preparedByEmployee', 'supplierInfo'),
            ], 200);
        });
    }

    // 📌 Delete delivery and cascade items
    public function destroy($id)
    {
        try {
            $delivery = Delivery::findOrFail($id);

            // 🚫 prevent deleting Approved deliveries
            if ($delivery->status === 'Approved') {
                return response()->json([
                    'message' => 'Approved deliveries cannot be deleted.'
                ], 403);
            }

            $delivery->delete(); // items cascade via model relationship

            return response()->json(['message' => 'Delivery deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete delivery',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
