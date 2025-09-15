<?php

namespace App\Models\Supply;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Library\LibUnit;
use App\Models\Library\LibBrand;

class ItemDelivery extends Model
{
    use HasFactory;

    protected $table = 'tbl_item_delivery';

    protected $primaryKey = 'item_delivery_id';

    protected $fillable = [
    'delivery_id','supply','item_type','stock_number','unit',
    'category','brand','model','additional_description','remarks',
    'quantity','unit_value','total_amount'
];


    public function delivery()
    {
        return $this->belongsTo(Delivery::class, 'delivery_id');
    }
public function brand()
{
    return $this->belongsTo(LibBrand::class, 'brand', 'Brand_Id');
}

public function unit()
{
    return $this->belongsTo(LibUnit::class, 'unit', 'Unit_Id');
}

}
