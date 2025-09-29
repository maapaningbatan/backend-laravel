<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;

class LibModule extends Model
{
    protected $table = 'lib_modules';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'module_name',
        'table_name',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at'
    ];
}
