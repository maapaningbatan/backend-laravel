<?php

namespace App\Models\Supply;

use App\Models\Library\LibDivision;
use App\Models\Library\LibOffice;
use App\Models\Library\LibPropertyChief;
use App\Models\Library\LibSupplyCustodian;
use App\Models\Tables\TblUser;
use Illuminate\Database\Eloquent\Model;

class SuppliesIssuance extends Model
{
    protected $table = 'tbl_supplies_issuance';

    protected $primaryKey = 'id';

    const CREATED_AT = 'date_created';

    const UPDATED_AT = 'date_updated';

    protected $fillable = [
        'ris_number',
        'pr_number',
        'date_issued',
        'region_id',
        'accountable_office',
        'accountable_division',
        'issued_by',
        'approved_by',
        'prepared_by',
        'remarks',
        'created_by',
        'updated_by',
        'isdeleted',
        'deleted_by',
        'date_deleted',
    ];

    public function office()
    {
        return $this->belongsTo(LibOffice::class, 'accountable_office', 'id');
    }

    public function division()
    {
        return $this->belongsTo(LibDivision::class, 'accountable_division', 'id');
    }

    public function issuer()
    {
        return $this->belongsTo(LibSupplyCustodian::class, 'issued_by', 'id')
            ->with('employee');
    }

    public function approver()
    {
        return $this->belongsTo(LibPropertyChief::class, 'approved_by', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(TblUser::class, 'created_by', 'id');
    }

    public function items()
    {
        return $this->hasMany(StockCard::class, 'supplies_issuance_id', 'id');
    }
}
