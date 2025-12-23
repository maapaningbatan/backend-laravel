<?php

use App\Http\Controllers\Library\SupplyCustodianController;
use Illuminate\Support\Facades\Route;


Route::prefix('supply-custodians')->group(function () {
    Route::get('/', [SupplyCustodianController::class, 'index']);
    Route::get('/{id}', [SupplyCustodianController::class, 'show']);
    Route::post('/', [SupplyCustodianController::class, 'store']);
    Route::put('/{id}', [SupplyCustodianController::class, 'update']);
    Route::delete('/{id}', [SupplyCustodianController::class, 'destroy']);
});
