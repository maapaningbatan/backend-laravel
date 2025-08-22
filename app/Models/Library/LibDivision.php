<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibDivision extends Model
{
    use SoftDeletes;

    protected $table = 'lib_division';
    protected $primaryKey = 'Division_Id';
    public $timestamps = false;

    const DELETED_AT = 'date_deleted';

    protected $fillable = [
        'Division',
        'Division_Desc',
        'Office_Id',
        'Region',
        'created_by',
        'date_created',
        'updated_by',
        'date_updated',
        'deleted_by',
    ];

    protected $dates = [
        'date_created',
        'date_updated',
        'date_deleted',
    ];
}
