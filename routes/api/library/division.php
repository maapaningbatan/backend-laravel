<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Library\DivisionController;

Route::prefix('divisions')->group(function () {
    Route::get('/', [DivisionController::class, 'index']);                // all active
    Route::get('/archive', [DivisionController::class, 'archived']);      // archived list
    Route::get('/office/{officeId}', [DivisionController::class, 'byOffice']); // by office
    Route::get('/{id}', [DivisionController::class, 'show']);             // single record
    Route::post('/', [DivisionController::class, 'store']);               // create
    Route::put('/{id}', [DivisionController::class, 'update']);           // update
    Route::patch('/{id}/archive', [DivisionController::class, 'archive']); // archive record
    Route::patch('/{id}/restore', [DivisionController::class, 'restore']); // restore record
    Route::delete('/{id}/force', [DivisionController::class, 'forceDelete']); // permanent delete
});


