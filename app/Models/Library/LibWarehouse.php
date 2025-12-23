<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;

class LibWarehouse extends Model
{

    protected $table = 'lib_warehouses';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'warehouse_code',
        'warehouse_desc',
        'is_archived',
        'region',

    ];
}
