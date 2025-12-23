<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Library\LibDivision;
use App\Models\Library\LibEmployee;
use App\Models\Library\LibOffice;

class LibEmployeeAssignment extends Model
{
    use SoftDeletes;

    protected $table = 'lib_employee_assignment';

    protected $fillable = [
        'employee_id',
        'designation',
        'office_id',
        'division_id',
        'start_date',
        'end_date',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    // Relationships
    public function office()
    {
        return $this->belongsTo(LibOffice::class, 'office_id');
    }

    public function division()
    {
        return $this->belongsTo(LibDivision::class, 'division_id');
    }

    public function employee()
    {
        return $this->belongsTo(LibEmployee::class, 'employee_id');
    }
}
