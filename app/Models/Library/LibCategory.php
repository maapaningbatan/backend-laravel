<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;

class LibCategory extends Model
{
    protected $table = 'lib_categories';

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'category_desc',
        'category_code',
        'created_by',
        'updated_by',
        'region_id',
        'is_archived'
    ];


    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
