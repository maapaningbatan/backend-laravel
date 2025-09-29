<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;

class LibPermission extends Model
{
    protected $table = 'lib_permissions';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'userlevel_id',
        'module_id',
        'can_add',
        'can_edit',
        'can_view',
        'can_delete',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at'
    ];

    // Optional: Eloquent relationships
    public function module()
    {
        return $this->belongsTo(LibModule::class, 'module_id');
    }

    public function userLevel()
    {
        return $this->belongsTo(LibUserLevel::class, 'userlevel_id');
    }
}
