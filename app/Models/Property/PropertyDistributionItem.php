<?php

namespace App\Models\Property;

use Illuminate\Database\Eloquent\Model;

class PropertyDistributionItem extends Model {
    protected $table = 'tbl_property_distribution_items';

    protected $fillable = [
        'distribution_id',
        'serial_no',
        'item_status'
    ];

        public function distribution()
    {
        return $this->belongsTo(PropertyDistribution::class, 'distribution_id');
    }

    public function issuanceItem()
    {
        return $this->belongsTo(PropertyIssuanceItem::class, 'issuance_item_id');
    }
}

