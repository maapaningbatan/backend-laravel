<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Library\RespCenterController;

Route::prefix('resp-centers')->group(function () {
    Route::get('/', [RespCenterController::class, 'index']);             // Active Models
    Route::get('/archive', [RespCenterController::class, 'archived']);   // Archived Models
    Route::get('/{id}', [RespCenterController::class, 'show']);          // Single Model
    Route::post('/', [RespCenterController::class, 'store']);            // Create new Model
    Route::put('/{id}', [RespCenterController::class, 'update']);        // Update Model
    Route::put('/{id}/archive', [RespCenterController::class, 'archive']); // Archive Model
    Route::patch('/{id}/restore', [RespCenterController::class, 'restore']); // Restore Model
    Route::delete('/{id}/force', [RespCenterController::class, 'forceDelete']); // Permanent delete
});
