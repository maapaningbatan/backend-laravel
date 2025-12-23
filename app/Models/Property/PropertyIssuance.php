<?php

namespace App\Models\Property;

use Illuminate\Database\Eloquent\Model;

class PropertyIssuance extends Model
{
    // Explicit table name
    protected $table = 'tbl_property_issuance';

    // Primary key
    protected $primaryKey = 'id';

    // Timestamps are present
    public $timestamps = true;

    // Mass assignable fields
    protected $fillable = [
        'issuance_no',
        'delivery_id',
        'requester_id',
        'supply_custodian_id',
        'approved_by_id',
        'remarks',
        'status',
        'created_by',
        'updated_by',
    ];

        public function items()
    {
        return $this->hasMany(PropertyIssuanceItem::class, 'issuance_id');
    }

    public function requester()
    {
        return $this->belongsTo(LibEmployee::class, 'requester_id');
    }

    public function custodian()
    {
        return $this->belongsTo(LibEmployee::class, 'supply_custodian_id');
    }

    public function approver()
    {
        return $this->belongsTo(LibEmployee::class, 'approved_by_id');
    }

    public function delivery()
    {
        return $this->belongsTo(Delivery::class, 'delivery_id');
    }

    public function distributions()
{
    return $this->hasMany(PropertyDistribution::class, 'issuance_id');
}

}
