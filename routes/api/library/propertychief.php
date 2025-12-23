<?php

use App\Http\Controllers\Library\PropertyChiefController;
use Illuminate\Support\Facades\Route;


Route::prefix('property_chiefs')->group(function () {
    Route::get('/', [PropertyChiefController::class, 'index']);       // List all chiefs
    Route::post('/', [PropertyChiefController::class, 'store']);      // Add new chief
    Route::get('{id}', [PropertyChiefController::class, 'show']);     // Get one
    Route::put('{id}', [PropertyChiefController::class, 'update']);   // Update
    Route::delete('{id}', [PropertyChiefController::class, 'destroy']); // Delete
});
