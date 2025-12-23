<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;

class LibAcquisitionMode extends Model
{
    protected $table = 'lib_acquisition_modes';
    protected $primaryKey = 'id';

    protected $fillable = [
        'acquisition_desc',
        'is_archived',
        'created_by',
        'date_created',
        'updated_by',
        'date_updated',
        'is_deleted',
        'deleted_by',
        'date_deleted',
    ];

    public $timestamps = false;
}
