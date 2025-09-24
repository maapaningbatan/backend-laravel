<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;

class LibFund extends Model
{
    protected $table ='lib_fund';

    protected $primaryKey='Fund_Id';

    public $incrementing=true;

    protected $keyType='int';

    protected $fillable=[
        "Fund_Code",
        'Fund_Description',
    ];
}
