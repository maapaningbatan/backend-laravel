<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;

class LibStatusOfAppointment extends Model
{
    protected $table = 'lib_status_of_appointment';

    protected $primaryKey = 'SOA_Id';

    public $timestamps = false; // since you have created_at and updated_at but probably manually set

    protected $fillable = [
        'Status_of_Appointment',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by',
    ];
}
