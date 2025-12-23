<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Property\{PropertyIssuance, PropertyIssuanceItem};
use App\Models\{PropertyCard, SemiExCard};
use App\Models\Library\LibArticleDescription;
use App\Models\Library\LibAccount;

class PropertyIssuanceController extends Controller
{
    /**
     * ======================
     * 1️⃣ GET /property-issuance
     * ======================
     * List all issuance records
     */
public function index()
{
    $ppe = DB::table('tbl_property_issuance as i')
        ->leftJoin('tbl_property_items as pi', 'pi.issuance_id', '=', 'i.id')
        ->leftJoin('tbl_property_card as pc', 'pc.item_delivery_id', '=', 'pi.item_delivery_id')
        ->leftJoin('lib_article_descriptions as a', 'pi.article_id', '=', 'a.id')
        ->leftJoin('lib_units as u', 'pi.unit_id', '=', 'u.id')
        ->leftJoin('lib_brands as b', 'pi.brand_id', '=', 'b.id')
        ->leftJoin('lib_models as m', 'pi.model_id', '=', 'm.id')
        ->leftJoin('lib_funds as f', 'pi.fund_id', '=', 'f.id')
        ->where('pc.is_selected', 1)
        ->select(
            'i.id',
            'i.issuance_no',
            'i.delivery_id',
            'i.requester_id',
            'i.supply_custodian_id',
            'i.approved_by_id',
            'i.status',
            'i.created_at',
            'i.updated_at',
            'i.remarks',
            DB::raw('COALESCE(a.article_desc, pi.general_description) as supplies_desc'),
            'pi.quantity',
            'pi.unit_value',
            DB::raw("(pi.quantity * pi.unit_value) as line_total"),
            DB::raw("'PPE' as category")
        );

    $semi = DB::table('tbl_property_issuance as i')
        ->leftJoin('tbl_property_items as pi', 'pi.issuance_id', '=', 'i.id')
        ->leftJoin('tbl_semi_expendable_cards as se', 'se.item_delivery_id', '=', 'pi.item_delivery_id')
        ->leftJoin('lib_article_descriptions as a', 'pi.article_id', '=', 'a.id')
        ->leftJoin('lib_units as u', 'pi.unit_id', '=', 'u.id')
        ->leftJoin('lib_brands as b', 'pi.brand_id', '=', 'b.id')
        ->leftJoin('lib_models as m', 'pi.model_id', '=', 'm.id')
        ->leftJoin('lib_funds as f', 'pi.fund_id', '=', 'f.id')
        ->whereNotNull('se.item_delivery_id')
        ->select(
            'i.id',
            'i.issuance_no',
            'i.delivery_id',
            'i.requester_id',
            'i.supply_custodian_id',
            'i.approved_by_id',
            'i.status',
            'i.created_at',
            'i.updated_at',
            'i.remarks',
            DB::raw('COALESCE(a.article_desc, pi.general_description) as supplies_desc'),
            'pi.quantity',
            'pi.unit_value',
            DB::raw("(pi.quantity * pi.unit_value) as line_total"),
            DB::raw("'Semi-Expendable' as category")
        );

    // Combine PPE + Semi-Ex rows with union
    $unionQuery = $ppe->union($semi);

    // Group per issuance to compute totals
    $issuances = DB::query()->fromSub($unionQuery, 'q')
        ->groupBy(
            'q.id',
            'q.issuance_no',
            'q.delivery_id',
            'q.requester_id',
            'q.supply_custodian_id',
            'q.approved_by_id',
            'q.status',
            'q.created_at',
            'q.updated_at',
            'q.remarks',
            'q.category'
        )
        ->select(
            'q.id',
            'q.issuance_no',
            'q.delivery_id',
            'q.requester_id',
            'q.supply_custodian_id',
            'q.approved_by_id',
            'q.status',
            'q.created_at',
            'q.updated_at',
            'q.remarks',
            DB::raw('MAX(q.supplies_desc) as supplies_desc'),
            DB::raw('SUM(q.quantity) as receipt_qty'),
            DB::raw('SUM(q.line_total) as total_amount'),
            'q.category'
        )
        ->orderByDesc('q.created_at')
        ->get();

    return response()->json(['data' => $issuances]);
}


    /**
     * ======================
     * 2️⃣ GET /property-issuance/available
     * ======================
     */
    public function availableItems()
    {
        $ppeItems = DB::table('tbl_property_card as pc')
        ->join('tbl_item_deliveries as i', 'pc.item_delivery_id', '=', 'i.id')
        ->join('tbl_delivery as d', 'i.delivery_id', '=', 'd.id')
        ->leftJoin('lib_suppliers as s', 'd.supplier_id', '=', 's.id')
        ->leftJoin('lib_supplies as ls', 'i.supply_id', '=', 'ls.id')
        ->leftJoin('lib_itemtype as t', 'i.item_type', '=', 't.id')
        ->leftJoin('lib_brands as b', 'i.brand_id', '=', 'b.id')
        ->leftJoin('lib_models as m', 'i.model_id', '=', 'm.id')
        ->leftJoin('lib_units as u', 'i.unit_id', '=', 'u.id')
        ->where('d.status', 'Approved')
        ->where(function ($query) {
            $query->whereNull('pc.is_selected')->orWhere('pc.is_selected', 0);
        })
        ->where('pc.balance', '>', 0)
        ->select([
            'd.id as delivery_id',
            'd.purchase_order_number',
            's.supplier_name',
            'i.id as item_delivery_id',
            'ls.supplies_desc as item_description',
            't.itemtype_desc as item_type',
            'b.brand_desc as brand_name',
            'm.model_desc as model_name',
            'u.unit_code as unit_name',
            'pc.receipt_qty',
            'pc.balance as available_balance',
            DB::raw("'PPE' as category"),
            'i.unit_value',
        ]);

    // SEMI-EX ITEMS
$semiExItems = DB::table('tbl_semi_expendable_cards as se')
    ->join('tbl_item_deliveries as i', 'se.item_delivery_id', '=', 'i.id')
    ->join('tbl_delivery as d', 'i.delivery_id', '=', 'd.id')
    ->leftJoin('lib_suppliers as s', 'd.supplier_id', '=', 's.id')
    ->leftJoin('lib_supplies as ls', 'i.supply_id', '=', 'ls.id')
    ->leftJoin('lib_itemtype as t', 'i.item_type', '=', 't.id')
    ->leftJoin('lib_brands as b', 'i.brand_id', '=', 'b.id')
    ->leftJoin('lib_models as m', 'i.model_id', '=', 'm.id')
    ->leftJoin('lib_units as u', 'i.unit_id', '=', 'u.id')
    ->where('d.status', 'Approved')
    ->where(function ($q) {
        $q->whereNull('se.is_selected')->orWhere('se.is_selected', 0);
    })
    ->where('se.balance', '>', 0)
    ->select([
        'd.id as delivery_id',
        'd.purchase_order_number',
        's.supplier_name',
        'i.id as item_delivery_id',
        'ls.supplies_desc as item_description',
        't.itemtype_desc as item_type',
        'b.brand_desc as brand_name',
        'm.model_desc as model_name',
        'u.unit_code as unit_name',
        'se.receipt_qty',
        'se.balance as available_balance',
        DB::raw("'Semi-Expendable' as category"),
        'i.unit_value',
    ]);


    // Combine PPE and Semi-Ex items
    $items = $ppeItems->union($semiExItems)->orderBy('delivery_id', 'desc')->get();

    return response()->json(['data' => $items]);
}

public function getItem($id)
{
    $item = DB::table('tbl_item_deliveries as i')
        ->join('tbl_delivery as d', 'i.delivery_id', '=', 'd.id')
        ->leftJoin('lib_supplies as ls', 'i.supply_id', '=', 'ls.id')
        ->leftJoin('lib_itemtype as t', 'i.item_type', '=', 't.id')
        ->leftJoin('lib_suppliers as s', 'd.supplier_id', '=', 's.id')
        ->leftJoin('lib_units as u', 'i.unit_id', '=', 'u.id')
        ->leftJoin('lib_brands as b', 'i.brand_id', '=', 'b.id')
        ->leftJoin('lib_models as m', 'i.model_id', '=', 'm.id')
        ->leftJoin('tbl_property_card as pc', 'pc.item_delivery_id', '=', 'i.id')
        ->leftJoin('tbl_semi_expendable_cards as se', 'se.item_delivery_id', '=', 'i.id')
        ->select(
            // Delivery info
            'd.id as delivery_id',
            'd.purchase_order_number',
            'd.purchase_date',
            's.supplier_name',

            // Item info
            'i.id as item_delivery_id',
            'i.supply_id',
            'ls.supplies_desc as item_description',
            't.itemtype_desc as item_type',

            // Brand / Model / Unit
            'b.id as brand_id',
            'b.brand_desc as brand_name',
            'm.id as model_id',
            'm.model_desc as model_name',
            'u.id as unit_id',
            'u.unit_code as unit_name',

            // Quantity and cost
            'i.quantity',
            'i.unit_value',
            'i.total_amount',
            'i.additional_description',

            // Computed balance
            DB::raw('COALESCE(pc.balance, se.balance, 0) as available_balance'),

            // Placeholder fields for frontend to fill
            DB::raw('NULL as article_id'),
            DB::raw('NULL as article_code'),
            DB::raw('NULL as article_desc'),
            DB::raw('NULL as fund_id'),
            DB::raw('NULL as fund_name'),
            DB::raw('NULL as estimated_useful_life'),
            DB::raw('NULL as account_desc'),
            DB::raw('NULL as warranty'),
            DB::raw('NULL as type'),
            DB::raw('NULL as general_description')
        )
        ->where('i.id', $id)
        ->first();

    if (!$item) {
        return response()->json(['message' => 'Item not found.'], 404);
    }

    return response()->json(['data' => $item], 200);
}
    /**
     * ======================
     * 3️⃣ POST /property-issuance/store
     * ======================
     */
    public function store(Request $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $validated = $request->validate([
                    'delivery_id' => 'nullable|integer',
                    'requester_id' => 'nullable|integer',
                    'supply_custodian_id' => 'nullable|integer',
                    'approved_by_id' => 'nullable|integer',
                    'remarks' => 'nullable|string',
                    'items' => 'required|array|min:1',
                    'items.*.item_delivery_id' => 'required|integer',
                    'items.*.article_id' => 'required|integer|exists:lib_article_descriptions,id',
                    'items.*.quantity' => 'required|numeric|min:1',
                    'items.*.unit_value' => 'required|numeric|min:0',
                    'items.*.fund_id' => 'nullable|integer',
                    'items.*.acct_id' => 'nullable|integer',
                    'items.*.brand_id' => 'nullable|integer',
                    'items.*.model_id' => 'nullable|integer',
                    'items.*.unit_id' => 'nullable|integer',
                    'items.*.acquisition_date' => 'nullable|date',
                    'items.*.estimated_useful_life' => 'nullable|integer',
                    'items.*.account_desc' => 'nullable|string',
                    'items.*.warranty' => 'nullable|string',
                    'items.*.type' => 'nullable|string',
                    'items.*.additional_description' => 'nullable|string',
                    'items.*.general_description' => 'nullable|string',
                ]);

                // Create issuance header
                $issuanceId = DB::table('tbl_property_issuance')->insertGetId([
                    'issuance_no' => $this->generateIssuanceNo($validated['items']),
                    'delivery_id' => $validated['delivery_id'] ?? null,
                    'requester_id' => $validated['requester_id'] ?? null,
                    'supply_custodian_id' => $validated['supply_custodian_id'] ?? null,
                    'approved_by_id' => $validated['approved_by_id'] ?? null,
                    'remarks' => $validated['remarks'] ?? null,
                    'status' => 'draft',
                    'created_by' => auth()->id(),
                    'created_at' => now(),
                ]);

                foreach ($validated['items'] as $item) {
                    $article = DB::table('lib_article_descriptions')
                        ->select('article_code', 'ac_id_ppe', 'ac_id_ics')
                        ->where('id', $item['article_id'])
                        ->first();

$semiExists = DB::table('tbl_semi_expendable_cards')
    ->where('item_delivery_id', $item['item_delivery_id'])
    ->exists();

$acctId = $item['acct_id']
    ?? ($semiExists ? $article->ac_id_ics : $article->ac_id_ppe);

DB::table('tbl_property_items')->insert([
    'issuance_id' => $issuanceId,
    'item_delivery_id' => $item['item_delivery_id'],
    'article_id' => $item['article_id'],
    'brand_id' => $item['brand_id'] ?? null,
    'model_id' => $item['model_id'] ?? null,
    'unit_id' => $item['unit_id'] ?? null,
    'fund_id' => $item['fund_id'] ?? null,
    'acct_id' => $acctId,
    'quantity' => $item['quantity'],
    'unit_value' => $item['unit_value'],
    'total_amount' => $item['quantity'] * $item['unit_value'],
    'acquisition_date' => $item['acquisition_date'] ?? now(),
    'estimated_useful_life' => $item['estimated_useful_life'] ?? 3,
    'account_desc' => $item['account_desc'] ?? 'Auto-generated',
    'warranty' => $item['warranty'] ?? null,
    'type' => $item['type'] ?? ($semiExists ? 'Semi-Expendable' : 'PPE'),
    'additional_description' => $item['additional_description'] ?? null,
    'general_description' => $item['general_description'] ?? null,
    'created_by' => auth()->id(),
    'created_at' => now(),
]);

if ($semiExists) {
    DB::table('tbl_semi_expendable_cards')
        ->where('item_delivery_id', $item['item_delivery_id'])
        ->update(['is_selected' => 1]);
} else {
    DB::table('tbl_property_card')
        ->where('item_delivery_id', $item['item_delivery_id'])
        ->update(['is_selected' => 1]);
}
                }

                return response()->json([
                    'message' => 'Property issuance successfully saved.',
                    'data' => [
                        'issuance_id' => $issuanceId,
                        'issuance_no' => DB::table('tbl_property_issuance')->where('id', $issuanceId)->value('issuance_no'),
                    ]
                ]);
            });
        } catch (\Throwable $e) {
            \Log::error('❌ Property issuance failed:', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function generateIssuanceNo(array $items = [])
    {
        $year = now()->year;
        $shortYear = substr($year, -2);
        $user = auth()->user();
        $regionCode = optional($user->region)->region_code ?? 'REG';
        $articleCode = 'GEN';

        if (!empty($items)) {
            $first = $items[0];
            if (!empty($first['article_id'])) {
                $article = DB::table('lib_article_descriptions')->where('id', $first['article_id'])->first();
                $articleCode = strtoupper($article?->article_code ?? 'GEN');
            } elseif (!empty($first['acct_id'])) {
                $account = LibAccount::find($first['acct_id']);
                $articleCode = strtoupper($account?->account_code ?? 'GEN');
            }
        }

        $pattern = "{$regionCode}-NI-{$articleCode}-{$shortYear}-%";
        $latest = PropertyIssuance::whereYear('created_at', $year)
            ->where('issuance_no', 'like', $pattern)
            ->orderBy('id', 'desc')
            ->value('issuance_no');

        $next = $latest && preg_match('/(\d{4})$/', $latest, $m)
            ? str_pad(((int)$m[1]) + 1, 4, '0', STR_PAD_LEFT)
            : '0001';

        return sprintf('%s-NI-%s-%s-%s', $regionCode, $articleCode, $shortYear, $next);
    }

  public function showDetails($id)
{
    $issuance = DB::table('tbl_property_issuance as i')
        ->leftJoin('tbl_delivery as d', 'i.delivery_id', '=', 'd.id')
        ->leftJoin('tbl_property_items as pi', 'pi.issuance_id', '=', 'i.id')

        // SERIAL-BASED DISTRIBUTION
        ->leftJoin(
            'tbl_property_distribution_items as pdi',
            'pdi.issuance_item_id',
            '=',
            'pi.id'
        )

        ->leftJoin('lib_article_descriptions as a', 'pi.article_id', '=', 'a.id')
        ->leftJoin('lib_units as u', 'pi.unit_id', '=', 'u.id')
        ->leftJoin('lib_brands as b', 'pi.brand_id', '=', 'b.id')
        ->leftJoin('lib_models as m', 'pi.model_id', '=', 'm.id')
        ->leftJoin('lib_funds as f', 'pi.fund_id', '=', 'f.id')

        // Employees
        ->leftJoin('lib_employees as req', 'i.requester_id', '=', 'req.id')
        ->leftJoin('lib_employees as cust', 'i.supply_custodian_id', '=', 'cust.id')
        ->leftJoin('lib_employees as app', 'i.approved_by_id', '=', 'app.id')

        ->select(
            'i.id as issuance_id',
            'i.issuance_no',
            'i.delivery_id',
            'd.invoice_no as sales_invoice',
            'd.invoice_date',

            DB::raw("CONCAT(req.first_name, ' ', req.last_name) as requester_name"),
            DB::raw("CONCAT(cust.first_name, ' ', cust.last_name) as custodian_name"),
            DB::raw("CONCAT(app.first_name, ' ', app.last_name) as approver_name"),

            'i.remarks',
            'i.status',

            'pi.id as issuance_item_id',
            'a.article_desc',
            'pi.quantity',
            'pi.unit_value',
            DB::raw('(pi.quantity * pi.unit_value) as line_total'),

            // ✅ SAFE SERIAL-BASED COMPUTATION
            DB::raw('COUNT(DISTINCT pdi.id) as distributed_qty'),
            DB::raw('(pi.quantity - COUNT(DISTINCT pdi.id)) as remaining_qty'),

            'd.purchase_date',
            DB::raw('COALESCE(pi.general_description, a.article_desc) as general_description'),
            'u.unit_code as unit_name',
            'b.brand_desc as brand_name',
            'm.model_desc as model_name',
            'f.fund_desc as fund_name'
        )
        ->where('i.id', $id)
        ->groupBy(
            'i.id',
            'i.issuance_no',
            'i.delivery_id',
            'd.invoice_no',
            'd.invoice_date',
            'req.first_name',
            'req.last_name',
            'cust.first_name',
            'cust.last_name',
            'app.first_name',
            'app.last_name',
            'i.remarks',
            'i.status',
            'pi.id',
            'a.article_desc',
            'pi.quantity',
            'pi.unit_value',
            'd.purchase_date',
            'pi.general_description',
            'u.unit_code',
            'b.brand_desc',
            'm.model_desc',
            'f.fund_desc'
        )
        ->first();

    if (!$issuance) {
        return response()->json(['message' => 'Issuance not found'], 404);
    }

    return response()->json(['data' => $issuance]);
}


}
