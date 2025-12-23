<?php

namespace App\Models\Supply;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Library\LibUnit;
use App\Models\Library\LibBrand;
use App\Models\Library\LibModel;
use App\Models\Library\LibCategory;
use App\Models\Library\LibSupply;
use App\Models\Library\LibItemType;

class ItemDelivery extends Model
{
    use HasFactory;

    protected $table = 'tbl_item_deliveries';
    protected $primaryKey = 'id';

    protected $fillable = [
        'delivery_id','supply_id','item_type','stock_number','unit_id',
        'category_id','brand_id','model_id','additional_description','remarks',
        'quantity','unit_value','total_amount'
    ];

    // Relationships
    public function delivery() { return $this->belongsTo(Delivery::class, 'delivery_id'); }
    public function brand() { return $this->belongsTo(LibBrand::class, 'brand_id', 'id'); }
    public function unit() { return $this->belongsTo(LibUnit::class, 'unit_id', 'id'); }
    public function model() { return $this->belongsTo(LibModel::class, 'model_id', 'id'); }
    public function supply() { return $this->belongsTo(LibSupply::class, 'supply_id', 'id'); }
    public function category() { return $this->belongsTo(LibCategory::class, 'category_id', 'id'); }
    public function itemType()
{
    return $this->belongsTo(LibItemType::class, 'item_type', 'id');
}

    // Computed attributes
    protected $appends = [
        'supply_desc', 'unit_name', 'category_desc', 'brand_desc', 'model_desc'
    ];

    public function getSupplyDescAttribute() { return $this->supply->Supplies_Desc ?? 'N/A'; }
    public function getUnitNameAttribute() { return $this->unit->Unit_Type ?? 'N/A'; }
    public function getCategoryDescAttribute() { return $this->category->category_desc ?? 'N/A'; }
    public function getBrandDescAttribute() { return $this->brand->Brand_Description ?? 'N/A'; }
    public function getModelDescAttribute() { return $this->model->model_desc ?? 'N/A'; }
}
