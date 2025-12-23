<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;

class LibStatusofAppointment extends Model
{
    protected $table = 'lib_soa';
    protected $primaryKey = 'id';
    public $timestamps = false; // manually managing date_created and updated_at

    protected $fillable = [
        'status_appointment',
        'created_by',
        'date_created',
        'updated_by',
        'updated_at',
        'is_archived',
    ];
}
