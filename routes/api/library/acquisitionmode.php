<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Library\AcquisitionModeController;

Route::prefix('library')->group(function () {
    Route::get('/acquisition-modes', [AcquisitionModeController::class, 'index']);
});
