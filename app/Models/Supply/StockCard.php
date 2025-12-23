<?php

namespace App\Models\Supply;

use App\Models\Library\LibDivision;
use App\Models\Library\LibEmployee;
use App\Models\Library\LibItemType;
use App\Models\Library\LibOffice;
use App\Models\Library\LibSupplier;
use App\Models\Library\LibSupply;
use App\Models\Library\LibUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockCard extends Model
{
    use HasFactory;

    protected $table = 'tbl_stock_card';

    protected $primaryKey = 'id';

    protected $fillable = [
        'supply_id',
        'transaction_date',
        'entry_type',
        'reference_no',
        'receipt_qty',
        'issued_qty',
        'remaining_balance',
        'office_id',
        'division_id',
        'received_by',
        'balance',
        'unit_value',
        'unit',
        'total',
        'property_no',
        'ics_no',
        'supplier_id',
        'sale_invoice',
        'remarks',
        'supplies_issuance_id',
        'created_at',
        'updated_at',
        'delivery_id',
        'item_delivery_id',
        'item_type_id',
    ];

    protected $appends = [
        'unit_code',
        'brand_desc',
        'category_desc',
        'model_desc',
    ];

    // Relationships
    public function supply()
    {
        return $this->belongsTo(LibSupply::class, 'supply_id', 'id');
    }

    public function delivery()
    {
        return $this->belongsTo(Delivery::class, 'delivery_id', 'id');
    }

    public function itemtype()
    {
        return $this->belongsTo(LibItemType::class, 'item_type_id', 'id');
    }

    public function itemDelivery()
    {
        return $this->belongsTo(ItemDelivery::class, 'item_delivery_id', 'id');
    }

    public function unitRelation()
    {
        return $this->belongsTo(LibUnit::class, 'unit', 'id');
    }

    public function getUnitCodeAttribute()
    {
        return $this->unit?->unit_code ?? $this->itemDelivery?->unit?->unit_code ?? null;
    }

    public function getBrandDescAttribute()
    {
        return $this->itemDelivery->brand->brand_desc ?? null;
    }

    public function getCategoryDescAttribute()
    {
        return $this->itemDelivery->category->category_desc ?? null;
    }

    public function getModelDescAttribute()
    {
        return $this->itemDelivery->model->model_desc ?? null;
    }

    public function office()
    {
        return $this->belongsTo(LibOffice::class, 'office_id', 'id');
    }

    public function division()
    {
        return $this->belongsTo(LibDivision::class, 'division_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(LibSupplier::class, 'supplier_id', 'id');
    }

    public function receiver()
    {
        return $this->belongsTo(LibEmployee::class, 'received_by', 'id');
    }
    public function Issuance()
    {
        return $this->belongsTo(SuppliesIssuance::class, 'supplies_issuance_id', 'id');
    }
}
