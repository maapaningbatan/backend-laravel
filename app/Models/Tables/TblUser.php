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

    protected $fillable = [
        'Username',
        'Email_Address',
        'Password',
        'Activated',
        'Employee_PK',
        'Activated_By',
        'Activated_At',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $hidden = [
        'Password',
    ];

    public $timestamps = true;

    // Relationship to LibEmployee
    public function employee()
    {
        return $this->belongsTo(LibEmployee::class, 'employee_pk', 'Employee_PK');
    }
}
