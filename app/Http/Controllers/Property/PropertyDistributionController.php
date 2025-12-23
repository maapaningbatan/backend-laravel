<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyDistributionController extends Controller
{
    /**
     * ============================
     * STORE DISTRIBUTION
     * ============================
     */
    public function store(Request $request, $issuanceItemId)
    {
        $user = $request->user();

        if (!$user) {
            throw new \Exception('Authenticated user not found.');
        }

        $userId = $user->id;

        $request->validate([
            'accountable_employee_id' => 'required|exists:lib_employees,id',
            'subpar_employee_id'      => 'nullable|exists:lib_employees,id',
            'qty'                     => 'required|integer|min:1',
            'serials'                 => 'required|array',
            'serials.*'               => 'required|string|distinct',
            'par_date'                => 'required|date',
        ]);

        DB::transaction(function () use ($request, $issuanceItemId, $userId) {

            // 🔒 Lock issuance item
            $item = DB::table('tbl_property_items')
                ->where('id', $issuanceItemId)
                ->lockForUpdate()
                ->first();

            if (!$item) {
                throw new \Exception('Issuance item not found.');
            }

            // Remaining check (serial-based)
            $distributed = DB::table('tbl_property_distribution_items')
                ->where('issuance_item_id', $issuanceItemId)
                ->count();

            if ($request->qty > ($item->quantity - $distributed)) {
                throw new \Exception('Quantity exceeds remaining balance.');
            }

            if (count($request->serials) !== $request->qty) {
                throw new \Exception('Serial count does not match quantity.');
            }

            // Prevent duplicate serials
            $exists = DB::table('tbl_property_distribution_items')
                ->where('issuance_id', $item->issuance_id)
                ->whereIn('serial_number', $request->serials)
                ->exists();

            if ($exists) {
                throw new \Exception('Duplicate serial number detected.');
            }

            // Create distribution header
            $distributionId = DB::table('tbl_property_distributions')->insertGetId([
                'issuance_id'             => $item->issuance_id,
                'issuance_item_id'        => $issuanceItemId,
                'accountable_employee_id' => $request->accountable_employee_id,
                'subpar_employee_id'      => $request->subpar_employee_id,
                'qty'                     => $request->qty,
                'par_date'                => $request->par_date,
                'created_by'              => $userId,
                'created_at'              => now(),
            ]);

            // Determine item type
            $itemType = $this->determineItemType($item->unit_value);

            // Generate ONE PAR / ICS No
            $parIcsNo = $itemType === 'PPE'
                ? $this->generateParNo($userId)
                : $this->generateIcsNo($userId);

            // Insert serial-level items
            foreach ($request->serials as $serial) {

                $propertyNo = $this->generatePropertyNo(
                    $issuanceItemId,
                    $request->accountable_employee_id,
                    $userId,
                    $itemType
                );

                DB::table('tbl_property_distribution_items')->insert([
                    'distribution_id'         => $distributionId,
                    'issuance_id'             => $item->issuance_id,
                    'issuance_item_id'        => $issuanceItemId,
                    'property_no'             => $propertyNo,
                    'par_ics_no'              => $parIcsNo,
                    'accountable_employee_id' => $request->accountable_employee_id,
                    'subpar_employee_id'      => $request->subpar_employee_id,
                    'serial_number'           => $serial,
                    'created_at'              => now(),
                ]);
            }

            DB::table('tbl_property_items')
    ->where('id', $issuanceItemId)
    ->update([
        'distributed_qty' => DB::raw('distributed_qty + ' . $request->qty),
        'updated_at'      => now(),
    ]);
    
        });

        return response()->json(['message' => 'Distribution saved successfully.']);
    }

    /**
     * ============================
     * ITEM TYPE DETECTOR
     * ============================
     */
    private function determineItemType(float $unitValue): string
    {
        if ($unitValue >= 50000) return 'PPE';
        if ($unitValue >= 5000)  return 'SPHV';
        return 'SPLV';
    }

    /**
     * ============================
     * PROPERTY NUMBER GENERATOR
     * ============================
     */
    private function generatePropertyNo(
        int $issuanceItemId,
        int $accountableEmployeeId,
        int $userId,
        string $type
    ): string {

        $region = $this->getUserRegionCode($userId);
        $year   = now()->format('Y');
        $month  = now()->format('m');

        $item = DB::table('tbl_property_items as pi')
            ->join('lib_article_descriptions as a', 'pi.article_id', '=', 'a.id')
            ->join('lib_acct_codes as ac', 'pi.acct_id', '=', 'ac.id')
            ->where('pi.id', $issuanceItemId)
            ->select('pi.unit_value', 'a.article_code', 'ac.sub_major_acct_grp', 'ac.gen_ledger_acct')
            ->first();

        if (!$item) {
            throw new \Exception('Invalid issuance item.');
        }

        // PPE → EXISTING FORMAT
        if ($type === 'PPE') {

            $office = DB::table('lib_employees as e')
                ->join('lib_offices as o', 'e.office_id', '=', 'o.id')
                ->where('e.id', $accountableEmployeeId)
                ->value('o.office_code') ?? 'OFF';

            $prefix = "{$region}-{$item->article_code}-{$year}-{$item->sub_major_acct_grp}-{$item->gen_ledger_acct}-";

            $latest = DB::table('tbl_property_distribution_items')
                ->where('property_no', 'like', "{$prefix}%{$office}")
                ->lockForUpdate()
                ->orderByDesc('property_no')
                ->value('property_no');

            $next = $latest && preg_match('/-(\d{4})-/', $latest, $m)
                ? str_pad($m[1] + 1, 4, '0', STR_PAD_LEFT)
                : '0001';

            return "{$prefix}{$next}-{$office}";
        }

        // ICS FORMAT
        $prefix = "{$region}-{$type}-{$item->article_code}-{$year}-{$month}-";

        $latest = DB::table('tbl_property_distribution_items')
            ->where('property_no', 'like', "{$prefix}%")
            ->lockForUpdate()
            ->orderByDesc('property_no')
            ->value('property_no');

        $next = $latest && preg_match('/(\d{4})$/', $latest, $m)
            ? str_pad($m[1] + 1, 4, '0', STR_PAD_LEFT)
            : '0001';

        return "{$prefix}{$next}";
    }

    /**
     * ============================
     * PAR (PPE)
     * ============================
     */
    private function generateParNo(int $userId): string
    {
        $region = $this->getUserRegionCode($userId);
        $year   = now()->format('Y');
        $month  = now()->format('m');

        $prefix = "{$region}-NI-{$year}-{$month}-";

        $latest = DB::table('tbl_property_distribution_items')
            ->where('par_ics_no', 'like', "{$prefix}%")
            ->lockForUpdate()
            ->orderByDesc('par_ics_no')
            ->value('par_ics_no');

        $next = $latest && preg_match('/(\d{5})$/', $latest, $m)
            ? str_pad($m[1] + 1, 5, '0', STR_PAD_LEFT)
            : '00001';

        return "{$prefix}{$next}";
    }

    /**
     * ============================
     * ICS
     * ============================
     */
    private function generateIcsNo(int $userId): string
    {
        $region = $this->getUserRegionCode($userId);
        $year   = now()->format('Y');
        $month  = now()->format('m');

        $prefix = "{$region}-ICS-NI-{$year}-{$month}-";

        $latest = DB::table('tbl_property_distribution_items')
            ->where('par_ics_no', 'like', "{$prefix}%")
            ->lockForUpdate()
            ->orderByDesc('par_ics_no')
            ->value('par_ics_no');

        $next = $latest && preg_match('/(\d{5})$/', $latest, $m)
            ? str_pad($m[1] + 1, 5, '0', STR_PAD_LEFT)
            : '00001';

        return "{$prefix}{$next}";
    }

    /**
     * ============================
     * USER REGION
     * ============================
     */
    private function getUserRegionCode(int $userId): string
    {
        return DB::table('tbl_users as u')
            ->join('lib_regions as r', 'r.id', '=', 'u.region_id')
            ->where('u.id', $userId)
            ->value('r.region_code')
            ?? 'REG';
    }

    /**
     * ============================
     * HISTORY
     * ============================
     */
    public function history($issuanceItemId)
    {
        $rows = DB::table('tbl_property_distributions as d')
            ->join('lib_employees as ae', 'ae.id', '=', 'd.accountable_employee_id')
            ->leftJoin('lib_employees as se', 'se.id', '=', 'd.subpar_employee_id')
            ->leftJoin('tbl_property_distribution_items as di', 'di.distribution_id', '=', 'd.id')
            ->where('d.issuance_item_id', $issuanceItemId)
            ->groupBy('d.id','d.par_date','ae.first_name','ae.last_name','se.first_name','se.last_name')
            ->select(
                'd.id as distribution_id',
                'd.par_date',
                DB::raw("CONCAT(ae.first_name,' ',ae.last_name) as accountable_employee"),
                DB::raw("CONCAT(se.first_name,' ',se.last_name) as subpar_employee"),
                DB::raw('COUNT(di.id) as qty'),
                DB::raw("GROUP_CONCAT(di.serial_number SEPARATOR ', ') as serials"),
                DB::raw("GROUP_CONCAT(di.property_no SEPARATOR ', ') as property_no"),
                DB::raw('MAX(di.par_ics_no) as par_ics_no')
            )
            ->orderByDesc('d.id')
            ->get();

        return response()->json(['data' => $rows]);
    }
}
