<?php

namespace App\Models\Tables;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Models\Library\LibEmployee;
use App\Models\Library\LibRegion;
use App\Models\Library\LibOffice;
use App\Models\Library\LibDivision;

class TblUser extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'tbl_users';
    protected $primaryKey = 'id'; // ✅ now consistent with your schema
    public $timestamps = true;

    protected $fillable = [
        'username',
        'email',
        'password',
        'activated',
        'activated_by',
        'activated_at',
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
        'contact_number',
        'address',
        'upload_contract',
        'user_level_id',
        'center_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'employee_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /* ======================
       🔗 RELATIONSHIPS
    ====================== */

    // Employee history snapshots
    public function employeeHistory()
    {
        return $this->hasMany(LibEmployee::class, 'user_id', 'id')
                    ->orderBy('version_no', 'desc');
    }

    // Latest employee snapshot
    public function latestEmployee()
    {
        return $this->hasOne(LibEmployee::class, 'user_id', 'id')
                    ->latestOfMany('version_no');
    }

    // Region relationship
    public function region()
    {
        return $this->belongsTo(LibRegion::class, 'region_id', 'id');
    }

    // Office relationship
    public function office()
    {
        return $this->belongsTo(LibOffice::class, 'office_id', 'id');
    }

    // Division relationship
    public function division()
    {
        return $this->belongsTo(LibDivision::class, 'division_id', 'id');
    }

    // Center relationship
    public function center()
    {
        return $this->belongsTo(\App\Models\Library\LibCenter::class, 'center_id', 'id');
    }
}
