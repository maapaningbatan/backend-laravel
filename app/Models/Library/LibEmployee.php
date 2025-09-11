<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; // ✅ should extend Model, not Authenticatable
use App\Models\Tables\TblUser;

class LibEmployee extends Model
{
    use HasFactory;

    protected $table = 'lib_employee';
    protected $primaryKey = 'Employee_PK';
    public $timestamps = true;

    protected $fillable = [
        'User_Id',
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
        'created_by',
        'updated_by',
        'deleted_by',
        'delete_at',
        'effective_date',
        'expired_at',
        'version_no',
    ];

    protected $hidden = [];

    // ✅ Relationship to TblUser (a history row belongs to a single user)
    public function user()
    {
        return $this->belongsTo(TblUser::class, 'User_Id', 'User_Id');
    }

    // ✅ Accessor for full name
    public function getFullNameAttribute()
    {
        $middleInitial = $this->Middle_Name ? strtoupper(substr($this->Middle_Name, 0, 1)) . '.' : '';
        return trim("{$this->First_Name} {$middleInitial} {$this->Last_Name}");
    }
}
