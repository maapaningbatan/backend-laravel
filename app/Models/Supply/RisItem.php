<?php

namespace App\Models\Supply;

use Illuminate\Database\Eloquent\Model;

class RisItem extends Model
{
    protected $table = 'tbl_ris_items';
    protected $primaryKey = 'ris_item_id';
    public $timestamps = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
    'ris_id',
    'supply_id',
    'unit_id',
    'quantity_requested',
    'quantity_issued',
    'description',
    'remarks',
];

    public function ris()
    {
        return $this->belongsTo(RIS::class, 'ris_id', 'ris_id');
    }
}
