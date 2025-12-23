<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Library\UserLevelController;
use App\Http\Controllers\Library\UserLevelPermissionController;
use App\Http\Controllers\Library\ModuleController;




Route::middleware('auth:sanctum')->group(function () {
    // User Levels
    Route::get('/user-levels', [UserLevelController::class, 'index']);
    Route::get('/user-levels/{id}', [UserLevelController::class, 'show']);
    // User Level Permissions
    Route::get('/user-levels/{id}/permissions', [UserLevelPermissionController::class, 'index']);
    Route::put('/user-levels/{id}/permissions', [UserLevelPermissionController::class, 'update']);
    Route::post('/user-levels/{id}/permissions', [UserLevelPermissionController::class, 'store']);
    Route::get('/user-levels/{id}', [UserLevelPermissionController::class, 'show']);
    // Module
    Route::prefix('modules')->group(function () {
        Route::get('/', [ModuleController::class, 'index']);
        Route::post('/addModuleWithPermissions', [ModuleController::class, 'storeWithPermissions']);
        Route::delete('/{id}', [ModuleController::class, 'destroy']);
        Route::put('/{id}/restore', [ModuleController::class, 'restore']);
    });
});
