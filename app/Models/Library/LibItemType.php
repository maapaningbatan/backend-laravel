<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;

class LibItemType extends Model
{
    protected $table = 'lib_itemtype';

    protected $primaryKey = 'itemtype_id';

    protected $fillable = [
        'itemtype_name',
    ];
}
