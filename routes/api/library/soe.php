<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Library\StatusOfEmploymentController;

Route::prefix('status-of-employment')->group(function () {
    Route::get('/', [StatusOfEmploymentController::class, 'index']);           // Active SOE
    Route::get('/archive', [StatusOfEmploymentController::class, 'archived']); // Archived SOE
    Route::get('/{id}', [StatusOfEmploymentController::class, 'show']);        // Single record
    Route::post('/', [StatusOfEmploymentController::class, 'store']);          // Create new
    Route::put('/{id}', [StatusOfEmploymentController::class, 'update']);      // Update
    Route::put('/{id}/archive', [StatusOfEmploymentController::class, 'archive']); // Archive
    Route::patch('/{id}/restore', [StatusOfEmploymentController::class, 'restore']); // Restore
    Route::delete('/{id}/force', [StatusOfEmploymentController::class, 'forceDelete']); // Permanent delete
});
