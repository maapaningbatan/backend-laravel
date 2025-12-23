<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Library\SupplierController;

Route::prefix('suppliers')->group(function () {
    Route::get('/', [SupplierController::class, 'index']);               // Active suppliers
    Route::get('/archive', [SupplierController::class, 'archived']);     // List archived suppliers
    Route::get('/{id}', [SupplierController::class, 'show']);            // Show single supplier
    Route::post('/', [SupplierController::class, 'store']);              // Create new supplier
    Route::put('/{id}', [SupplierController::class, 'update']);          // Update supplier
    Route::put('/{id}/archive', [SupplierController::class, 'archive']); // Archive supplier
    Route::patch('/{id}/restore', [SupplierController::class, 'restore']); // Restore supplier
    Route::delete('/{id}/force', [SupplierController::class, 'forceDelete']); // Permanent delete
});
