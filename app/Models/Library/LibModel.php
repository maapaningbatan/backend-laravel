<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;
use App\Models\Supply\ItemDelivery;

class LibModel extends Model
{
    protected $table = 'lib_models';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'date_updated';

    protected $fillable = [
        'model_desc',
        'created_by',
        'updated_by',
        'is_deleted',
        'deleted_by',
        'is_archived',
    ];

    public function itemDeliveries()
    {
        return $this->hasMany(ItemDelivery::class, 'model_id', 'id');
    }
}
