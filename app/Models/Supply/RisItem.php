<?php

namespace App\Models\Supply;

use App\Models\Library\LibSupply;
use Illuminate\Database\Eloquent\Model;
use App\Models\Library\LibUnit;

class RisItem extends Model
{
    protected $table = 'tbl_ris_items';

    protected $primaryKey = 'id';

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
        return $this->belongsTo(RIS::class, 'ris_id', 'id');
    }

    public function supply()
    {
        return $this->belongsTo(LibSupply::class, 'supply_id', 'id');
    }
    public function unit()
{
    return $this->belongsTo(LibUnit::class, 'unit_id');
}

}
