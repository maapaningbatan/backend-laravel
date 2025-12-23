<?php

namespace App\Models\Property;

use Illuminate\Database\Eloquent\Model;

class PropertyDistribution extends Model {
    protected $table = 'tbl_property_distributions';

    protected $fillable = [
        'issuance_id',
        'issuance_item_id',
        'employee_id',
        'qty',
        'par_date',
        'remarks',
        'created_by'
    ];

    public function issuance()
    {
        return $this->belongsTo(PropertyIssuance::class, 'issuance_id');
    }

    public function issuanceItem()
    {
        return $this->belongsTo(PropertyIssuanceItem::class, 'issuance_item_id');
    }

    public function items()
    {
        return $this->hasMany(PropertyDistributionItem::class, 'distribution_id');
    }
}
