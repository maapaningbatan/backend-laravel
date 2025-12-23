<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Property\SemiExCardController;

Route::prefix('semi-expandable-cards')->group(function () {
    Route::get('/', [SemiExCardController::class, 'index']);
    Route::get('/{id}', [SemiExCardController::class, 'show']);
});

