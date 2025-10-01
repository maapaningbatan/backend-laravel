<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;

class LibSupplier extends Model
{
    protected $table = 'lib_suppliers';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'supplier_name',
        'supplier_address',
        'contact_person',
        'contact_no',
        'tin_no',
        'supplier_no',
        'supplier_email',
        'region_id',
        'created_by',
        'date_created',
        'updated_by',

    ];

    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'supplier', 'id');
    }
}
