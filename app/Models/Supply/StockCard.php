<?php

namespace App\Models\Supply;

use App\Models\Library\LibSupply;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockCard extends Model
{
    use HasFactory;

    protected $table = 'tbl_stock_card';

    protected $primaryKey = 'stock_card_id'; // correct primary key

    // Fillable fields must be in quotes and comma-separated
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
    ];

    // Relationship to supplies
   public function supply()
{
    return $this->belongsTo(LibSupply::class, 'supply_id', 'SuppliesID');
}

}
