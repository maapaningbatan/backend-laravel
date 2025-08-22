<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibCluster extends Model
{
    use SoftDeletes;

    protected $table = 'lib_cluster';
    protected $primaryKey = 'Cluster_Id';
    public $timestamps = false;

    const DELETED_AT = 'Date_Deleted';

    protected $fillable = [
        'Cluster',
        'Cluster_Desc',
        'Location',
        'Head_of_office',
        'Createdby',
        'DateCreated',
        'Updatedby',
        'Date_Updated',
        'Region',
        'IsDeleted',
        'Deletedby',
    ];

    protected $dates = [
        'DateCreated',
        'Date_Updated',
        'Date_Deleted',
    ];
}
