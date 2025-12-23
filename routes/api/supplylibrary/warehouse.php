<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Library\WarehouseController;


Route::prefix('warehouses')->group(function () {
    Route::get('/', [WarehouseController::class, 'index']);            // Active Units
    Route::get('/archive', [WarehouseController::class, 'archived']);   // Archived Units
    Route::get('/{id}', [WarehouseController::class, 'show']);          // Single Unit
    Route::post('/', [WarehouseController::class, 'store']);            // Create new Unit
    Route::put('/{id}', [WarehouseController::class, 'update']);        // Update Unit
    Route::put('/{id}/archive', [WarehouseController::class, 'archive']); // Archive Unit
    Route::patch('/{id}/restore', [WarehouseController::class, 'restore']); // Restore Unit
    Route::delete('/{id}/force', [WarehouseController::class, 'forceDelete']); // Permanent delete
});
