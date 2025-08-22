<?php

namespace App\Models\Tables;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class TblUser extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'tbl_user';
    protected $primaryKey = 'User_Id';

    protected $fillable = [
        'Username',
        'Email_Address',
        'Password',
        'Activated',
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
}
