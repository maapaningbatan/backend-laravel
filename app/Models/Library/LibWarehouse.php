<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;

class LibWarehouse extends Model
{

    protected $table = 'lib_warehouse';
    protected $primaryKey = 'Warehouse_ID';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'Warehouse_Code',
        'Warehouse_Name',
        'Remarks'

    ];
}
