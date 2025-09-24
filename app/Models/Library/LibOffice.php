<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibOffice extends Model
{
    use SoftDeletes;

    protected $table = 'lib_offices';
    protected $primaryKey = 'id';

    protected $fillable = [
        'bldg_id',
        'cluster_id',
        'office_code',
        'office_desc',
        'obs_head',
        'region_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    // Relationships
    public function region()
    {
        return $this->belongsTo(LibRegion::class, 'region_id', 'id');
    }

    public function cluster()
    {
        return $this->belongsTo(LibCluster::class, 'cluster_id', 'id');
    }
}
