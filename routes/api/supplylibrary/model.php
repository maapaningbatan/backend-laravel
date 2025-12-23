<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Library\ModelController;

Route::prefix('models')->group(function () {
    Route::get('/', [ModelController::class, 'index']);             // Active Models
    Route::get('/archive', [ModelController::class, 'archived']);   // Archived Models
    Route::get('/{id}', [ModelController::class, 'show']);          // Single Model
    Route::post('/', [ModelController::class, 'store']);            // Create new Model
    Route::put('/{id}', [ModelController::class, 'update']);        // Update Model
    Route::put('/{id}/archive', [ModelController::class, 'archive']); // Archive Model
    Route::patch('/{id}/restore', [ModelController::class, 'restore']); // Restore Model
    Route::delete('/{id}/force', [ModelController::class, 'forceDelete']); // Permanent delete
});
