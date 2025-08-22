<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibOffice extends Model
{
    use SoftDeletes;

    protected $table = 'lib_office';
    protected $primaryKey = 'Office_Id';
    public $timestamps = false; // we have manual date fields

    const DELETED_AT = 'date_deleted';

    protected $fillable = [
        'Office',
        'Office_Desc',
        'OBS_Head',
        'Region',
        'Cluster',
        'Bldg_Id',
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
