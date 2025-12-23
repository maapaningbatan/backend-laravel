<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;
use App\Models\Supply\Delivery;

class LibSupplier extends Model
{
    protected $table = 'lib_suppliers';
    protected $primaryKey = 'id';
    public $timestamps = true;


    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'date_updated';

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
        'updated_by',
        'is_archived',
    ];

    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'supplier', 'id');
    }
}
