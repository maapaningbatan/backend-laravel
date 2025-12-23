<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;

class LibSupply extends Model
{
    // Table name
    protected $table = 'lib_supplies'; // updated table name

    // Primary key
    protected $primaryKey = 'id'; // updated primary key
    public $incrementing = true;
    protected $keyType = 'int';

    // Disable default timestamps since we have custom column names
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    // Mass assignable fields
    protected $fillable = [
        'stock_no',
        'category_id',
        'supplies_desc',
        'unit_id',
        'unit_value',
        'supplies_reorder_point',
        'created_by',
        'updated_by',
        'is_active',
        'deleted_by',
        'deleted_at',
        'region_id',
        'supplies_img_path',
        'supplies_img_name',
        'is_archived'
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(LibCategory::class, 'category_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(LibUnit::class, 'unit_id', 'id');
    }
}
