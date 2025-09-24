<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibRegion extends Model
{
    use SoftDeletes;

    protected $table = 'lib_regions'; // plural for consistency
    protected $primaryKey = 'id';      // standardized primary key
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;         // use Laravel's created_at & updated_at



    protected $fillable = [
        'region_code',
        'region_desc',
        'location',
        'zip_code',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
