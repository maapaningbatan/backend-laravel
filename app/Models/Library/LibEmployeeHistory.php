<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibEmployeeHistory extends Model
{
     use SoftDeletes;
    protected $table = 'lib_employee_history';

    protected $fillable = [
        'employee_id',
        'position_id',
        'office_id',
        'division_id',
        'soa_id',
        'soe_id',
        'start_date',
        'end_date',
        'clearance',
        'remarks',
        'created_by',
        'created_at',
        'deleted_by',
        'deleted_at',
        'updated_by',
    ];

    public $timestamps = false;

    public function employee()
    {
        return $this->belongsTo(LibEmployee::class, 'employee_id');
    }
    public function position()
{
    return $this->belongsTo(LibPosition::class, 'position_id');
}

public function office()
{
    return $this->belongsTo(LibOffice::class, 'office_id');
}

public function division()
{
    return $this->belongsTo(LibDivision::class, 'division_id');
}

public function soa()
{
    return $this->belongsTo(LibStatusofAppointment::class, 'soa_id');
}

public function soe()
{
    return $this->belongsTo(LibStatusOfEmployment::class, 'soe_id');
}

}
