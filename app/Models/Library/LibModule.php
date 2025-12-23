<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibModule extends Model
{
    use SoftDeletes;

    protected $table = 'lib_modules';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'module_name',
        'table_name',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];
}
