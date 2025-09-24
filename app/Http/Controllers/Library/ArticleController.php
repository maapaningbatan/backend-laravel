<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibArticle;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = LibArticle::select(
            'Article_Id',
            'Article_Desc',
            'Property_Type',
            'EUL_YR'
        )
        ->where('AD_Active_Status', 1) // only active
        ->get();

        return response()->json($articles);
    }
}
