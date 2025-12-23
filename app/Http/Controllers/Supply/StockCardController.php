<?php

    namespace App\Http\Controllers\Supply;

    use App\Http\Controllers\Controller;
    use App\Models\Library\LibSupply;
    use App\Models\Supply\StockCard;
    use Illuminate\Http\Request;

    class StockCardController extends Controller
    {
        /**
         * Get stock cards for a specific supply (used in listing).
         */
        public function index(Request $request)
        {
            $query = StockCard::with([
                'supply.category',
                'unitRelation',
                'supplier',
                'itemtype',
                'itemDelivery.category',
                'itemDelivery.brand',
                'itemDelivery.model',
                'delivery',
            ])->orderBy('transaction_date', 'desc');

            if ($request->has('delivery_id')) {
                $query->where('delivery_id', $request->delivery_id);
            }

            if ($request->has('supply_id')) {
                $query->where('supply_id', $request->supply_id);
            }

            $records = $query->get()->map(function ($record) {
                return [
                    'stock_card_id' => $record->id,
                    'transaction_date' => $record->transaction_date,
                    'entry_type' => $record->entry_type,
                    'reference_no' => $record->reference_no,
                    'receipt_qty' => $record->receipt_qty,
                    'issued_qty' => $record->issued_qty,
                    'balance' => $record->balance,
                    'remaining_balance' => $record->remaining_balance,
                    'unit_value' => $record->unit_value,
                    'unit_id' => $record->unitRelation?->id ?? $record->unit,
                    'unit' => $record->unit_code,
                    'total' => $record->total,
                    'remarks' => $record->remarks,
                    'item_type_id' => $record->item_type_id,
                    'itemtype_name' => $record->itemtype?->itemtype_desc ?? '-',
                    'supplier_id' => $record->supplier?->id ?? null,
                    'supplier_name' => $record->supplier->supplier_name ?? null,
                    'sale_invoice' => $record->sale_invoice,
                    'invoice_date' => $record->delivery?->invoice_date,
                    'dr_no' => $record->delivery?->dr_no,
                    'brand_desc' => $record->itemDelivery?->brand?->brand_desc ?? null,
                    'model_desc' => $record->itemDelivery?->model?->model_desc ?? null,
                    'supply' => [
                        'id' => $record->supply?->id,
                        'supplies_desc' => $record->supply?->supplies_desc,
                        'stock_no' => $record->supply?->stock_no,
                        'category' => $record->supply?->category?->category_desc,
                        'supplies_reorder_point' => $record->supply?->Supplies_ReOrder_PT,
                    ],
                ];
            });

            return response()->json(['stockcards' => $records]);
        }

        /**
         * Show supply and all its stock cards.
         */
        public function show($id)
        {
            $supply = LibSupply::find($id);

            if (! $supply) {
                return response()->json(['message' => 'Supply not found'], 404);
            }

            $stockcards = StockCard::with([
                'supply',
                'office',
                'division',
                'itemtype',
                'supplier',
                'receiver',
                'itemDelivery.unit',
                'itemDelivery.brand',
                'itemDelivery.model',
            ])
                ->where('supply_id', $id)
                ->orderBy('transaction_date', 'asc')
                ->get()
                ->map(function ($record) {
                    return [
                        'stock_card_id' => $record->id,
                        'transaction_date' => $record->transaction_date,
                        'entry_type' => $record->entry_type,
                        'reference_no' => $record->reference_no,
                        'receipt_qty' => $record->receipt_qty,
                        'issued_qty' => $record->issued_qty,
                        'balance' => $record->balance,
                        'remaining_balance' => $record->remaining_balance,
                        'unit_value' => $record->unit_value,
                        'total' => $record->total,
                        'remarks' => $record->remarks,
                        'property_no' => $record->property_no,
                        'office_desc' => $record->office?->office_code,
                        'division_desc' => $record->division?->division_code,
                        'unit_id' => $record->itemDelivery?->unit?->id ?? null,
                        'unit_code' => $record->unit_code,
                        'brand_desc' => $record->itemDelivery?->brand?->brand_desc ?? null,
                        'model_desc' => $record->itemDelivery?->model?->model_desc ?? null,
                        'item_type_id' => $record->item_type_id,
                        'itemtype_name' => $record->itemtype?->itemtype_desc ?? '-',
                        'supplier_name' => $record->supplier->supplier_name ?? null,
                        'receiver' => $record->receiver?->full_name ?? null,
                    ];
                });

            return response()->json([
                'supply' => [
                    'id' => $supply->id,
                    'supplies_desc' => $supply->supplies_desc,
                    'stock_no' => $supply->stock_no,
                    'Supplies_ReOrder_PT' => $supply->Supplies_ReOrder_PT,
                    'Supplies_Qty' => $supply->Supplies_Qty,
                    'supplies_nonstock_qty' => $supply->supplies_nonstock_qty,
                    'supplies_reorder_point' => $supply?->Supplies_ReOrder_PT,
                ],
                'stockcards' => $stockcards,
            ]);
        }

        public function getPurchaseOrders()
        {
            $purchaseOrders = StockCard::with('delivery:id,purchase_order_number')
                ->select('delivery_id')
                ->whereNotNull('delivery_id')
                ->groupBy('delivery_id')
                ->havingRaw('SUM(remaining_balance) > 0') // only delivery_ids with remaining stock
                ->get()
                ->filter(fn ($sc) => $sc->delivery !== null)
                ->map(fn ($sc) => [
                    'id' => $sc->delivery->id,
                    'purchase_order_number' => $sc->delivery->purchase_order_number,
                ])
                ->values();

            return response()->json([
                'purchase_orders' => $purchaseOrders,
            ]);
        }

        public function printICS($id)
        {
            $stock = StockCard::with([
                'supply',
                'supplier',
                'receiver.position',
                'office',
                'division',
                'itemDelivery.unit',
                'Issuance.issuer.employee',
                'Issuance.approver.employee',
            ])->findOrFail($id);



            $issuerName = $stock->Issuance?->issuer?->employee?->full_name ?? 'N/A';
            $issuerPosition = $stock->Issuance?->issuer?->employee?->position_code ?? 'N/A';
            $issuerOffice = $stock->Issuance?->issuer?->employee?->office?->office_code ?? 'N/A';
            $issuerDivision = $stock->Issuance?->issuer?->employee?->division?->division_code ?? 'N/A';
            $approverName     = $stock->Issuance?->approver?->employee?->full_name ?? 'N/A';
            $approverPosition     = $stock->Issuance?->approver?->employee?->position_code ?? 'N/A';
            $approverOffice    = $stock->Issuance?->approver?->employee?->office?->office_code ?? 'N/A';
            $approverDivision     = $stock->Issuance?->approver?->employee?->division?->division_code ?? 'N/A';

            return response()->json([
                'ics_no' => $stock->ics_no,
                'ics_date' => $stock->transaction_date,
                'entity_name' => $stock->office?->office_code
                            .($stock->division?->division_code ? ' - '.$stock->division->division_code : ''),
                'fund_cluster' => '01',
                'quantity' => $stock->issued_qty,
                'unit' => $stock->unitRelation?->unit_code,
                'unit_cost' => $stock->unit_value,
                'total_cost' => $stock->total,
                'description' => $stock->supply->supplies_desc,
                'property_no' => $stock->property_no,
                'estimated_life' => '—',
                'receiver' => $stock->receiver?->full_name,
                'receiver_position' => $stock->receiver?->position_code,
                'receiver_office' => $stock->receiver?->office?->office_code,
                'receiver_division' => $stock->receiver?->division?->division_code,
                'issuer' => $issuerName,
                'issuer_position' => $issuerPosition,
                'issuer_office' => $issuerOffice,
                'issuer_division' => $issuerDivision,
                'approver' => $approverName,
                'approver_position' => $approverPosition,
                'approver_office' => $approverOffice,
                'approver_division' => $approverDivision,
                'supplier' => $stock->supplier?->supplier_name,
                'sale_invoice' => $stock->sale_invoice,
                'po_no' => $stock->delivery?->purchase_order_number,
                'ris_no' => $stock->reference_no,
            ]);
        }
    }
