<?php

namespace App\Models\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Library\LibSupply;
use App\Models\Supply\ItemDelivery;
use App\Models\Supply\Delivery;

class SemiExCard extends Model
{
    use HasFactory;

    protected $table = 'tbl_semi_expandable_card';
    protected $primaryKey = 'semi_expandable_card_id';

    protected $fillable = [
        'supply_id',
        'transaction_date',
        'entry_type',
        'reference_no',
        'receipt_qty',
        'issued_qty',
        'office_id',
        'balance',
        'unit_value',
        'unit',
        'total',
        'property_no',
        'remarks',
        'created_at',
        'updated_at',
        'delivery_id',
        'itemdelivery_id'
    ];

    public function supply()
    {
        return $this->belongsTo(LibSupply::class, 'supply_id', 'SuppliesID');
    }
    public function itemdelivery()
{
    return $this->belongsTo(ItemDelivery::class, 'ItemDelivery_id', 'item_delivery_id');
}
public function delivery()
{
    return $this->belongsTo(Delivery::class,'delivery_id','delivery_id');
}


}
