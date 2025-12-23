<?php
use App\Http\Controllers\Supply\SuppliesIssuanceController;

Route::middleware('auth:sanctum')->prefix('supplies-issuance')->group(function () {
    Route::get('/', [SuppliesIssuanceController::class, 'index']);
    Route::post('/', [SuppliesIssuanceController::class, 'store']);
    Route::get('/{id}', [SuppliesIssuanceController::class, 'show']);
    Route::put('/{id}', [SuppliesIssuanceController::class, 'update']);
    Route::delete('/{id}', [SuppliesIssuanceController::class, 'destroy']);
});

