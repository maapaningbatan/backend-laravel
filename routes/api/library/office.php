<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Library\OfficeController;

Route::prefix('offices')->group(function () {
    Route::get('/', [OfficeController::class, 'index']);
    Route::get('/archive', [OfficeController::class, 'archived']);
    Route::get('/{id}', [OfficeController::class, 'show']);
    Route::post('/', [OfficeController::class, 'store']);
    Route::put('/{id}', [OfficeController::class, 'update']);
    Route::put('/{id}/archive', [OfficeController::class, 'archive']);
    Route::patch('/{id}/restore', [OfficeController::class, 'restore']);
    Route::delete('/{id}/force', [OfficeController::class, 'forceDelete']);

    Route::get('/region/{regionId}', [OfficeController::class, 'getByRegion']);
});

