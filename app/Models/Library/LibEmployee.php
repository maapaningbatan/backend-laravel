<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibEmployee extends Model
{
    use HasFactory;

    protected $table = 'lib_employees';

    protected $primaryKey = 'id';

    protected $appends = ['full_name', 'position_code'];

    public $timestamps = true;

    protected $fillable = [
        'employee_no',
        'honorific',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'title',
        'sex',
        'position_id',
        'region_id',
        'office_id',
        'division_id',
        'cluster_id',
        'soa_id',
        'soe_id',
        'upload_contract',
        'start_date',
        'end_date',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    // Relationships
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
        return $this->belongsTo(LibStatusofEmployment::class, 'soe_id');
    }

    public function region()
    {
        return $this->belongsTo(LibRegion::class, 'region_id');
    }

    public function cluster()
    {
        return $this->belongsTo(LibCluster::class, 'cluster_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim(
            ($this->honorific ? $this->honorific.' ' : '').
            $this->first_name.' '.
            ($this->middle_name ? substr($this->middle_name, 0, 1).'. ' : '').
            $this->last_name.
            ($this->suffix ? ' '.$this->suffix : '').
            ($this->title ? ' '.$this->title : '')
        );
    }

    public function getPositionCodeAttribute()
    {
        return $this->position?->position_code;
    }
}
