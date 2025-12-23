<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Library\ClusterController;



Route::get('/clusters', [ClusterController::class, 'index']);
