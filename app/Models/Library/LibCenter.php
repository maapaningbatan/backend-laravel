<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;

class LibCenter extends Model
{
    protected $table = 'lib_centers';
    protected $primaryKey = 'id';
    public $timestamps = false; // since we’re using manual date fields

    protected $fillable = [
        'center',
        'center_desc',
        'createdby',
        'datecreated',
        'updatedby',
        'dateupdated',
        'deletedby',
        'datedeleted',
    ];
}
