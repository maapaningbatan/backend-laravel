<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;

class LibUnit extends Model
{

    protected $table = 'lib_unit';
    protected $primaryKey = 'Unit_Id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'Unit_Type',
        'Unit_Description'
    ];
      public function itemDeliveries()
    {
        return $this->hasMany(\App\Models\Supply\ItemDelivery::class, 'unit', 'Unit_Id');
    }
}
