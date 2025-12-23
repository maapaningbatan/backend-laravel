<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;



class LibUnit extends Model
{

    protected $table = 'lib_units';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'unit_code',
        'unit_desc',
        'is_archived',

    ];
      public function itemDeliveries()
    {
        return $this->hasMany(\App\Models\Supply\ItemDelivery::class, 'unit', 'id');
    }
}
