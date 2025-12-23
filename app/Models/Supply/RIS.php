<?php

namespace App\Models\Supply;

use App\Models\Library\LibEmployee;
use App\Models\Library\LibFund;
use App\Models\Library\LibOffice;
use App\Models\Library\LibDivision;
use App\Models\Library\LibPropertyChief;
use App\Models\Tables\TblUser;
use Illuminate\Database\Eloquent\Model;
use App\Models\Supply\RisItem;
use App\Models\Library\LibSupply;
use App\Models\Library\LibRegion;

class RIS extends Model
{
    protected $table = 'tbl_ris';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'ris_number',
        'responsibility_center',
        'region_id',
        'office_id',
        'division_id',
        'fund_cluster_id',
        'ris_date',
        'purpose',
        'requested_by_id',
        'received_by_id',
        'approved_by_id',
        'status',
    ];

    public function items()
    {
        return $this->hasMany(RisItem::class, 'ris_id', 'id');
    }
    public function supply()
    {
        return $this->belongsTo(LibSupply::class, 'supply_id', 'id');
    }
    public function office()
    {
        return $this->belongsTo(LibOffice::class, 'office_id', 'id');
    }
    public function division()
    {
        return $this->belongsTo(LibDivision::class, 'division_id', 'id');
    }
    public function region()
    {
        return $this->belongsTo(LibRegion::class, 'region_id', 'id');
    }
    public function fundCluster()
    {
        return $this->belongsTo(LibFund::class, 'fund_cluster_id', 'id');
    }
    public function requestedBy()
    {
        return $this->belongsTo(LibEmployee::class, 'requested_by_id','id');
    }

    // Received By
    public function receivedBy()
    {
        return $this->belongsTo(LibEmployee::class, 'received_by_id');
    }

    // Approved By (Property Chief)
    public function approvedBy()
    {
        return $this->belongsTo(LibPropertyChief::class, 'approved_by_id','id');
    }
    public function createdBy()
    {
        return $this->belongsTo(TblUser::class, 'created_by');
    }
}
