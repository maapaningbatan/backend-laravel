<?php

namespace App\Models\Tables;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Library\LibEmployee;
use App\Models\Library\LibRegion;
use App\Models\Library\LibOffice;
use App\Models\Library\LibDivision;
use App\Models\Library\LibCenter;
use App\Models\Library\LibPosition;
use App\Models\Library\LibUserLevel;
use App\Models\Library\LibPermission;



class TblUser extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

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
    'first_name',
    'middle_name',
    'last_name',
    'suffix',
    'position_id',
    'region_id',
    'office_id',
    'division_id',
    'cluster_id',
    'user_level_id',
    'center_id',
    'created_by',
    'updated_by',
    'deleted_by'
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $dates = ['deleted_at'];

    /* ======================
       🔗 RELATIONSHIPS
    ====================== */

 

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
        return $this->belongsTo(LibCenter::class, 'center_id', 'id');
    }
    // Position relationship
    public function position()
    {
        return $this->belongsTo(LibPosition::class, 'position_id', 'id');
    }
public function userLevel()
{
    return $this->belongsTo(LibUserLevel::class, 'user_level_id', 'id');
}
public function permissions()
{
    return $this->hasMany(LibPermission::class, 'userlevel_id', 'id');
}
}
