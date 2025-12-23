<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Property\PropertyCardController;

Route::get('/property-cards', [PropertyCardController::class, 'index']);
Route::get('/property-cards/{id}', [PropertyCardController::class, 'show']);
