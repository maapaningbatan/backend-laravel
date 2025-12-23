<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Library\ArticleController;


Route::get('/articles', [ArticleController::class, 'index']);
