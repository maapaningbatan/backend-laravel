<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Library\PositionController;

Route::prefix('positions')->group(function () {
    Route::get('/', [PositionController::class, 'index']);           // Active positions
    Route::get('/archive', [PositionController::class, 'archived']); // List archived positions
    Route::get('/{id}', [PositionController::class, 'show']);        // Show single
    Route::post('/', [PositionController::class, 'store']);          // Create
    Route::put('/{id}', [PositionController::class, 'update']);      // Update
    Route::put('/{id}/archive', [PositionController::class, 'archive']);       // Archive
    Route::patch('/{id}/restore', [PositionController::class, 'restore']);     // Restore
    Route::delete('/{id}/force', [PositionController::class, 'forceDelete']); // Permanent delete
});




