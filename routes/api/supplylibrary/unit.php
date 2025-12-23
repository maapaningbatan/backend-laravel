<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Library\UnitController;

Route::prefix('units')->group(function () {
    Route::get('/', [UnitController::class, 'index']);             // Active Units
    Route::get('/archive', [UnitController::class, 'archived']);   // Archived Units
    Route::get('/{id}', [UnitController::class, 'show']);          // Single Unit
    Route::post('/', [UnitController::class, 'store']);            // Create new Unit
    Route::put('/{id}', [UnitController::class, 'update']);        // Update Unit
    Route::put('/{id}/archive', [UnitController::class, 'archive']); // Archive Unit
    Route::patch('/{id}/restore', [UnitController::class, 'restore']); // Restore Unit
    Route::delete('/{id}/force', [UnitController::class, 'forceDelete']); // Permanent delete
});
