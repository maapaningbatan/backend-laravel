<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Library\CenterController;



Route::apiResource('centers', CenterController::class);
