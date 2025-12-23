<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;

class LibStatusOfEmployment extends Model
{
    protected $table = 'lib_soe';

    protected $primaryKey = 'id';

    public $timestamps = false; // since you have created_at and updated_at but probably manually set

   protected $fillable = [
        'soe',
        'soe_date',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by',
        'is_archived',
    ];
}
