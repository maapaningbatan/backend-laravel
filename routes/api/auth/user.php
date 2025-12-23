
<?php

use App\Http\Controllers\Library\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'user']);
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users',[UserController::class,'store']);
    Route::patch('/users/{id}/toggle', [UserController::class, 'toggleActivation']);
    Route::get('/users/archive', [UserController::class, 'archived']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::put('users/{id}/archive', [UserController::class, 'archive']);

});
