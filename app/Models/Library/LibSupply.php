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
    ];
}
