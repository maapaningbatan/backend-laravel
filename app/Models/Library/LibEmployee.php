<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class LibEmployee extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = 'lib_employee';

    protected $primaryKey = 'Employee_PK';

    public $timestamps = true;

    protected $fillable = [
        'Honorifics',
        'First_Name',
        'Middle_Name',
        'Last_Name',
        'Suffix',
        'Title',
        'Sex',
        'Contact_Number',
        'Address',
        'Employee_Id',
        'Position',
        'Region',
        'Office',
        'Division',
        'Cluster',
        'SOA',
        'SOE',
        'User_Level',
        'Upload_Contract',
        'password',
        'created_by',
        'updated_by',
        'deleted_by',
        'delete_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
