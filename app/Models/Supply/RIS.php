<?php

namespace App\Models\Supply;

use Illuminate\Database\Eloquent\Model;

class Ris extends Model
{
    protected $table = 'tbl_ris';
    protected $primaryKey = 'ris_id';
    public $timestamps = true;

    protected $fillable = [
        'ris_number',
        'responsibility_center',
        'purpose',
        'requested_by',
        'received_by',
        'approved_by',
        'region',
        'office',
        'fund_cluster',
        'ris_date',
        'status'
    ];

    public function items()
    {
        return $this->hasMany(RisItem::class, 'ris_id', 'ris_id');
    }
}
