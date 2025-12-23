<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Supply\RISController;


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/ris', [RISController::class, 'index']);
    Route::get('/ris/{id}', [RISController::class, 'show']);
    Route::get('/ris/{id}/print', [RISController::class, 'print']); // ✅ your print route
    Route::post('/ris', [RISController::class, 'store']);
    Route::put('/ris/{id}', [RISController::class, 'update']);
    Route::delete('/ris/{id}', [RISController::class, 'destroy']);
});
