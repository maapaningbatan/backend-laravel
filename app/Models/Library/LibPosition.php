<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibPosition extends Model
{
    use SoftDeletes;

    protected $table = 'lib_position'; // Adjust if your table name is different

    protected $primaryKey = 'Position_Id';

    // SoftDeletes will use this column by default, so specify it
    const DELETED_AT = 'delete_at';

    protected $fillable = [
        'Position_Id',
        'Position',
        'Position_Desc',
        'deleted_by',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'delete_at',
        'created_at',
        'updated_at',
    ];
}
