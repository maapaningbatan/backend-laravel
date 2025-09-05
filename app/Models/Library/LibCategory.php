<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;

class LibCategory extends Model
{

    protected $table = 'lib_category';
    protected $primaryKey = 'category_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'category_desc',
        'category_code'
    ];
}
