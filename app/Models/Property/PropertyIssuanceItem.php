<?php

namespace App\Models\Property;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Library\{
    LibArticleDescription,
    LibBrand,
    LibModel,
    LibUnit,
    LibFund,
    LibAccount
};

class PropertyIssuanceItem extends Model
{
    protected $table = 'tbl_property_items';

    protected $fillable = [
        'issuance_id',
        'item_delivery_id',
        'article_id',
        'brand_id',
        'model_id',
        'unit_id',
        'fund_id',
        'acct_id',
        'quantity',
        'distributed_qty',
        'unit_value',
        'total_amount',
        'acquisition_date',
        'estimated_useful_life',
        'account_desc',
        'warranty',
        'type',
        'additional_description',
        'general_description',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'quantity' => 'integer',
        'unit_value' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    /* ============================
     | RELATIONSHIPS
     |============================ */

    public function issuance()
    {
        return $this->belongsTo(PropertyIssuance::class, 'issuance_id');
    }

    public function article()
    {
        return $this->belongsTo(LibArticleDescription::class, 'article_id');
    }

    public function brand()
    {
        return $this->belongsTo(LibBrand::class, 'brand_id');
    }

    public function model()
    {
        return $this->belongsTo(LibModel::class, 'model_id');
    }

    public function unit()
    {
        return $this->belongsTo(LibUnit::class, 'unit_id');
    }

    public function fund()
    {
        return $this->belongsTo(LibFund::class, 'fund_id');
    }

    public function account()
    {
        return $this->belongsTo(LibAccount::class, 'acct_id');
    }

        public function distributions() {
        return $this->hasMany(PropertyDistribution::class, 'issuance_item_id');
    }

    public function getRemainingQtyAttribute(): int
    {
        return max(0, $this->quantity - $this->distributed_qty);
    }
}
