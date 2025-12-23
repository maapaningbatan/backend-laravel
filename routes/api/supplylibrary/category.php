<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Library\CategoryController;

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);             // Active Categories
    Route::get('/archive', [CategoryController::class, 'archived']);   // Archived Categories
    Route::get('/{id}', [CategoryController::class, 'show']);          // Single Category
    Route::post('/', [CategoryController::class, 'store']);            // Create new Category
    Route::put('/{id}', [CategoryController::class, 'update']);        // Update Category
    Route::put('/{id}/archive', [CategoryController::class, 'archive']); // Archive Category
    Route::patch('/{id}/restore', [CategoryController::class, 'restore']); // Restore Category
    Route::delete('/{id}/force', [CategoryController::class, 'forceDelete']); // Permanent delete
});
