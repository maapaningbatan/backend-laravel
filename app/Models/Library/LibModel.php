<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;

class LibModel extends Model
{

    protected $table = 'lib_model';
    protected $primaryKey = 'model_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'model_desc',
    ];
}
