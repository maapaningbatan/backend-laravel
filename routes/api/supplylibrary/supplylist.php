<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Library\SupplyController;




Route::prefix('supplies')->group(function () {
    Route::get('/', [SupplyController::class, 'index']);                 // Active supplies
    Route::get('/archived', [SupplyController::class, 'archived']);      // Archived supplies

    // ✅ Action routes first (to prevent route conflicts)
    Route::put('/{id}/archive', [SupplyController::class, 'archive']);
    Route::patch('/{id}/restore', [SupplyController::class, 'restore']);
    Route::delete('/{id}/force', [SupplyController::class, 'forceDelete']);

    // Then basic CRUD routes
    Route::get('/{id}', [SupplyController::class, 'show']);
    Route::post('/', [SupplyController::class, 'store']);
    Route::put('/{id}', [SupplyController::class, 'update']);
});
