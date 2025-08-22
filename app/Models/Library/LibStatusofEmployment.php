<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;

class LibStatusOfEmployment extends Model
{
    protected $table = 'lib_status_of_employment';

    protected $primaryKey = 'SOE_Id';

    public $timestamps = false; // since you have created_at and updated_at but probably manually set

   protected $fillable = [
        'Soe',
        'Soe_Date',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
