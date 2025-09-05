<?php

namespace App\Models\Supply;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemDelivery extends Model
{
    use HasFactory;

    protected $table = 'tbl_item_delivery';
    protected $primaryKey = 'item_delivery_id'; // correct primary key

    protected $fillable = [
    'delivery_id','supply','item_type','stock_number','unit',
    'category','brand','model','additional_description','remarks',
    'quantity','unit_value','total_amount'
];


    public function delivery()
    {
        return $this->belongsTo(Delivery::class, 'delivery_id');
    }
}
