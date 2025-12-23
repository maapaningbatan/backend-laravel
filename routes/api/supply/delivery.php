<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Supply\DeliveryController;
use App\Http\Controllers\Library\ItemTypeController;
use App\Http\Controllers\Library\ArticleController;
//Item Type (PPE, Semi-Expendable , Supplies and Non-consumable)
Route::get('/itemtypes', [ItemTypeController::class, 'index']);
Route::get('/itemtypes/{id}', [ItemTypeController::class, 'show']);
Route::get('/articles/{id}/compute-eul', [ArticleController::class, 'computeEul']);

//Delivery
 Route::get('/delivery/next-code', [DeliveryController::class, 'getNextCode']);
    Route::prefix('delivery')->group(function () {
        Route::get('/', [DeliveryController::class, 'index']);
        Route::post('/add', [DeliveryController::class, 'store']);
        Route::delete('/{id}', [DeliveryController::class, 'destroy']);
        Route::patch('/edit/{id}', [DeliveryController::class, 'update']);
        Route::get('/{id}', [DeliveryController::class, 'show']);
        Route::post('/{id}/approve', [DeliveryController::class, 'approve']);
    });
