<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Library\LibEmployee;

class LibPropertyChief extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lib_property_chiefs';

    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function employee()
    {
        return $this->belongsTo(LibEmployee::class, 'employee_id','id');
    }
}
