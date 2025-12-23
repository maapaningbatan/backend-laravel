<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;
use App\Models\Library\LibRegion;

class LibRespCenter extends Model
{
    protected $table = 'lib_resp_centers';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'resp_center_code',
        'resp_center_desc',
        'resp_center_office',
        'created_by',
        'date_created',
        'updated_by',
        'date_updated',
        'region_id',
        'is_archived',
    ];

    protected $dates = [
        'date_created',
        'date_updated',
    ];
    public function region()
    {
        return $this->belongsTo(LibRegion::class, 'region_id', 'id');
    }
}
