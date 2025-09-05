<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibPosition extends Model
{
    use SoftDeletes;

    protected $table = 'lib_position';
    protected $primaryKey = 'Position_Id';
    public $incrementing = true;
    protected $keyType = 'int';

    // Laravel expects 'deleted_at', but our column is 'delete_at'
    const DELETED_AT = 'delete_at';

    protected $fillable = [
        'Position',
        'Position_Desc',
        'deleted_by',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'delete_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
