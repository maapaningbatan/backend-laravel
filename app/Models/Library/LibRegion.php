<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibRegion extends Model
{
    use SoftDeletes;

    protected $table = 'lib_region';
    protected $primaryKey = 'Region_Id';
    public $timestamps = false; // keeping this, since you're not using Laravel's created_at/updated_at

    // Tell Laravel which column is the deleted marker
    const DELETED_AT = 'date_deleted';

    protected $fillable = [
        'Region',
        'Region_Desc',
        'Location',
        'Zip_Code',
        'created_by',
        'date_created',
        'updated_by',
        'date_updated',
        'deleted_by',
    ];

    // Let Laravel cast the date_deleted as a Carbon date
    protected $dates = [
        'date_deleted',
        'date_created',
        'date_updated',
    ];
}
