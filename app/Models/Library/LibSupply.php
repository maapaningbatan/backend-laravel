<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;

class LibSupply extends Model
{

    protected $table = 'lib_supplieslist';
    protected $primaryKey = 'SuppliesID';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'StockNo',
        'CategoryID',
        'BrandID',
        'Supplies_Desc',
        'UnitID',
        'UnitValue',
        'Supplies_Qty',
        'supplies_nonstock_qty',
        'Supplies_ReOrder_PT',
        'isActive'
    ];

    public function category()
{
    return $this->belongsTo(LibCategory::class, 'CategoryID', 'category_id');
}
    public function unit()
{
    return $this->belongsTo(LibUnit::class, 'UnitID', 'Unit_Id');
}
}
