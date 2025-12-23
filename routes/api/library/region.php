<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Library\RegionController;

Route::prefix('regions')->group(function () {
    Route::get('/', [RegionController::class, 'index']);               // Active regions
    Route::get('/archive', [RegionController::class, 'archived']);     // Archived regions
    Route::get('/{id}', [RegionController::class, 'show']);            // View region
    Route::post('/', [RegionController::class, 'store']);              // Create region
    Route::put('/{id}', [RegionController::class, 'update']);          // Update region
    Route::put('/{id}/archive', [RegionController::class, 'archive']); // Archive region
    Route::patch('/{id}/restore', [RegionController::class, 'restore']); // Restore archived region
    Route::delete('/{id}/force', [RegionController::class, 'forceDelete']); // Permanent delete
});
