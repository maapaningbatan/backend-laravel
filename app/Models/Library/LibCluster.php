<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibCluster extends Model
{
    use SoftDeletes;

    protected $table = 'lib_clusters';
    protected $primaryKey = 'id';
    public $timestamps = false;

    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'cluster_code',
        'cluster_desc',
        'region_id',
        'location',
        'head_of_office',
        'created_by',
        'date_created',
        'updated_by',
        'date_updated',
        'deleted_by',
        'deleted_at',
        'is_deleted',
    ];

    protected $dates = [
        'date_created',
        'date_updated',
        'deleted_at',
    ];
}
