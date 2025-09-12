<?php

namespace App\Models\Property;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Library\LibSupply;

class SemiExCard extends Model
{
    use HasFactory;

    protected $table = 'tbl_semi_expandable_card';

    protected $primaryKey = 'semi_expandable_card_id'; // correct primary key

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
