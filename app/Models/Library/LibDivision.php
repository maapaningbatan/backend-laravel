<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibDivision extends Model
{
    use HasFactory;

    protected $table = 'lib_divisions';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'office_id',
        'region_id',
        'division_code',
        'division_desc',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Relationships
     */
    public function office()
    {
        return $this->belongsTo(LibOffice::class, 'office_id');
    }

    public function region()
    {
        return $this->belongsTo(LibRegion::class, 'region_id');
    }
}
