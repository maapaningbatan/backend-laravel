<?php

namespace App\Models\Tables;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Library\LibEmployee;

class TblUser extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'tbl_user';
    protected $primaryKey = 'User_Id';
    public $timestamps = true;

    protected $fillable = [
        'Username',
        'Email_Address',
        'Password',
        'Activated',
        'Activated_By',
        'Activated_At',
        'Employee_Id',
        'Honorifics',
        'First_Name',
        'Middle_Name',
        'Last_Name',
        'Suffix',
        'Title',
        'Sex',
        'Position',
        'Region',
        'Office',
        'Division',
        'Cluster',
        'Contact_Number',
        'Address',
        'Upload_Contract',
        'User_Level',
        'created_by',
        'updated_by',
        'deleted_by',
        'employee_pk',

    ];

    protected $hidden = [
        'Password',
        'remember_token',
    ];

    // ✅ Relationship to LibEmployee (a user can have many history snapshots)
    public function employeeHistory()
    {
        return $this->hasMany(LibEmployee::class, 'User_Id', 'User_Id')
                    ->orderBy('version_no', 'desc');
    }

    // ✅ Helper to get latest employee snapshot
    public function latestEmployee()
    {
        return $this->hasOne(LibEmployee::class, 'User_Id', 'User_Id')
                    ->latestOfMany('version_no');
    }
}
