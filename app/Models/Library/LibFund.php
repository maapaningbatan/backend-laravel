<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;

class LibFund extends Model
{
    protected $table ='lib_funds';

    protected $primaryKey='id';

    public $incrementing=true;

    protected $keyType='int';

    protected $fillable=[
        "fund_code",
        'fund_desc',
        'is_archived',
    ];
}
