<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;

class LibBrand extends Model
{
    // Table name
    protected $table = 'lib_brand';

    // Primary key
    protected $primaryKey = 'Brand_Id';
    public $incrementing = true;
    protected $keyType = 'int';

    // Disable timestamps since your table uses custom columns
    public $timestamps = false;

    // Mass assignable fields
    protected $fillable = [
        'Brand_Description',
        'Createdby',
        'DateCreated',
        'Updatedby',
        'Date_Updated',
    ];

    // Relationships
    public function itemDeliveries()
    {
        return $this->hasMany(\App\Models\Supply\ItemDelivery::class, 'brand', 'Brand_Id');
    }
}
