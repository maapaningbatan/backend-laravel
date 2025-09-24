<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibPosition extends Model
{
    use SoftDeletes;

    protected $table = 'lib_positions';
    protected $primaryKey = 'id';

    protected $fillable = [
        'position_code',
        'position_desc',
        'region_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}

