<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tables\TblUser;

class LibEmployee extends Model
{
    use HasFactory;

    // ✅ Table and PK
    protected $table = 'lib_employees';
    protected $primaryKey = 'id';   // auto-increment bigint PK
    public $timestamps = true;

    // ✅ Fillable columns (match table + RegisterController)
protected $fillable = [
        'user_id',
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
        'version_no',
        'effective_date',
        'created_by',
        'updated_by',
    ];

    // ✅ Relationship to TblUser (belongsTo)
    public function user()
    {
        return $this->belongsTo(TblUser::class, 'user_id', 'id');
    }

    // ✅ Accessor for full name
    public function getFullNameAttribute()
    {
        $middleInitial = $this->middle_name ? strtoupper(substr($this->middle_name, 0, 1)) . '.' : '';
        return trim("{$this->first_name} {$middleInitial} {$this->last_name}");
    }
}
