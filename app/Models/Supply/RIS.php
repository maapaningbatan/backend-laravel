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
        'region_id',
        'office_id',
        'fund_cluster',
        'ris_date',
        'purpose',
        'requested_by_id',
        'received_by_id',
        'approved_by_id',
        'status',
    ];

    public function items()
    {
        return $this->hasMany(RisItem::class, 'ris_id', 'ris_id')->with('supply');
    }
}
