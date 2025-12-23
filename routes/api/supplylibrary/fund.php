<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Library\FundController;

Route::prefix('funds')->group(function () {
    Route::get('/', [FundController::class, 'index']);                 // Active funds
    Route::get('/archive', [FundController::class, 'archived']);       // Archived funds
    Route::get('/{id}', [FundController::class, 'show']);              // Single fund
    Route::post('/', [FundController::class, 'store']);                // Create new fund
    Route::put('/{id}', [FundController::class, 'update']);            // Update fund
    Route::put('/{id}/archive', [FundController::class, 'archive']);   // Archive fund
    Route::patch('/{id}/restore', [FundController::class, 'restore']); // Restore fund
    Route::delete('/{id}/force', [FundController::class, 'forceDelete']); // Permanent delete
});
