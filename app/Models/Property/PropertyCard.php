<?php

namespace App\Models\Property;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Library\LibSupply;
use App\Models\Supply\ItemDelivery;

class PropertyCard extends Model
{
    use HasFactory;

    protected $table = 'tbl_property_card';
    protected $primaryKey = 'property_card_id';

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
        'ItemDelivery_id',
    ];

    public function supply()
    {
        return $this->belongsTo(LibSupply::class, 'supply_id', 'SuppliesID');
    }
    public function itemdelivery()
{
    return $this->belongsTo(ItemDelivery::class, 'ItemDelivery_id', 'item_delivery_id');
}


}
