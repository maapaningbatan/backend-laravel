<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Library\LibPermission;


class LibUserLevel extends Model
{
    protected $table = 'lib_user_levels';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'userlevel',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];



public function permissions()
{
    return $this->hasMany(LibPermission::class, 'userlevel_id', 'id')
                ->with('module'); // eager-load module
}



}

