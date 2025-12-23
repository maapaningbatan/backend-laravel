<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;

class LibAcctCode extends Model
{
    protected $table = 'lib_acct_codes';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'acct_code',
        'acct_desc',
        'eul',
        'sub_major_acct_grp',
        'gen_ledger_acct',
        'created_by',
        'updated_by',
    ];

    // Optional reverse relationship
    public function articles()
    {
        return $this->hasMany(LibArticleDescription::class, 'ac_id_ppe', 'id');
    }
}
