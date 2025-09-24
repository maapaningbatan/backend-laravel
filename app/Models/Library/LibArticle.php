<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;

class LibArticle extends Model
{
    protected $table = 'lib_article_desc';

    protected $primaryKey = 'Article_Id'; // important

    public $timestamps = false; // if your table doesn't have created_at / updated_at

    protected $fillable = [
        'Article_Id',
        'Article_Desc',
        'Property_Type',
        'AD_Active_Status',
        'AC_ID_PPE',
        'AC_ID_ICS',
        'AcctCategory_Id',
        'EUL_YR',
        'EUL_YR_SPHV',
        'EUL_YR_SPLV',

    ];
}
