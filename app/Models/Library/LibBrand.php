<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;

class LibBrand extends Model
{

    protected $table = 'lib_brand';
    protected $primaryKey = 'Brand_Id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'Brand_Description',
        'Createdby',
        'DateCreated',
        'Updatedby',
        'Date_Updated',
    ];
}
