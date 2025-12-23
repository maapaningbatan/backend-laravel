<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;

class LibBrand extends Model
{
    // Table name
    protected $table = 'lib_brands';

    // Primary key
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';


    public $timestamps = false;

    protected $fillable = [
        'brand_desc',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'is_archived',
    ];

    // Relationships
    public function itemDeliveries()
    {
        return $this->hasMany(\App\Models\Supply\ItemDelivery::class, 'brand_id', 'id');
    }
}
