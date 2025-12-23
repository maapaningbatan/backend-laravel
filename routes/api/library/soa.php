<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Library\StatusOfAppointmentController;

Route::prefix('status-of-appointments')->group(function () {
    Route::get('/', [StatusOfAppointmentController::class, 'index']);           // Active SOA
    Route::get('/archive', [StatusOfAppointmentController::class, 'archived']); // Archived SOA
    Route::get('/{id}', [StatusOfAppointmentController::class, 'show']);        // Single record
    Route::post('/', [StatusOfAppointmentController::class, 'store']);          // Create new
    Route::put('/{id}', [StatusOfAppointmentController::class, 'update']);      // Update
    Route::put('/{id}/archive', [StatusOfAppointmentController::class, 'archive']); // Archive
    Route::patch('/{id}/restore', [StatusOfAppointmentController::class, 'restore']); // Restore
    Route::delete('/{id}/force', [StatusOfAppointmentController::class, 'forceDelete']); // Permanent delete
});
