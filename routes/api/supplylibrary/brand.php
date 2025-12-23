<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Library\BrandController;

Route::prefix('brands')->group(function () {
    Route::get('/', [BrandController::class, 'index']);                 // Active warehouses
    Route::get('/archive', [BrandController::class, 'archived']);       // Archived warehouses
    Route::get('/{id}', [BrandController::class, 'show']);              // Single warehouse
    Route::post('/', [BrandController::class, 'store']);                // Create new warehouse
    Route::put('/{id}', [BrandController::class, 'update']);            // Update warehouse
    Route::put('/{id}/archive', [BrandController::class, 'archive']);   // Archive warehouse
    Route::patch('/{id}/restore', [BrandController::class, 'restore']); // Restore warehouse
    Route::delete('/{id}/force', [BrandController::class, 'forceDelete']); // Permanent delete
});
