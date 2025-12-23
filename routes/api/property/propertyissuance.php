<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Property\PropertyIssuanceController;

Route::middleware('auth:sanctum')
    ->prefix('property-issuance')
    ->group(function () {
        Route::get('/', [PropertyIssuanceController::class, 'index']);                 // 📋 Fetch all issuances
        Route::get('/available-items', [PropertyIssuanceController::class, 'availableItems']);  // 🧾 Modal data
        Route::get('/item/{id}', [PropertyIssuanceController::class, 'getItem']);      // 🔍 Fetch single item
        Route::post('/store', [PropertyIssuanceController::class, 'store']);           // 💾 Save issuance
        Route::post('/{id}/approve', [PropertyIssuanceController::class, 'approve']);  // ✅ Approve issuance
        Route::delete('/{id}', [PropertyIssuanceController::class, 'destroy']);        // 🗑️ Delete issuance
        Route::get('/{id}/details', [PropertyIssuanceController::class, 'showDetails']);

    });
