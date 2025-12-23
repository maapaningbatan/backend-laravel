<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;

class LibArticle extends Model
{
    // Table name
    protected $table = 'lib_article_descriptions';

    // Primary key
    protected $primaryKey = 'id';

    // Enable timestamps (since your table has created_at and updated_at)
    public $timestamps = true;

    // Columns that are mass assignable
    protected $fillable = [
        'article_code',
        'article_desc',
        'ac_id_ppe',
        'ac_id_ics',
        'eul_yr',
        'eul_yr_sphv',
        'eul_yr_splv',
        'created_by',
        'updated_by',
    ];

    // Optional: if your created_at / updated_at column names are custom
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
