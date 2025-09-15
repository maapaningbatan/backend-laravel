<?php

namespace App\Models\Supply;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RIS extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_ris';
    protected $primaryKey = 'ris_id';
    public $timestamps = true;

    protected $fillable = [
        'ris_number','responsibility_center','region','office','fund_cluster',
        'ris_date','purpose','requested_by','received_by','approved_by','status'
    ];

    protected $casts = [
        'ris_date' => 'date',
        'status' => 'string',
    ];
}
