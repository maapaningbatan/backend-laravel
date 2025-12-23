<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Library\EmployeeHistoryController;
use App\Http\Controllers\Library\EmployeeController;
use App\Http\Controllers\Library\EmployeeAssignmentController;


Route::prefix('employees')->group(function () {
    Route::get('/', [EmployeeController::class, 'index']);
    Route::post('/', [EmployeeController::class, 'store']);
    Route::get('/{id}', [EmployeeController::class, 'show']);
    Route::put('/{id}', [EmployeeController::class, 'update']);
    Route::delete('/{id}', [EmployeeController::class, 'destroy']);
    Route::get('/{employeeId}/history', [EmployeeHistoryController::class, 'index']);
    Route::post('/{employeeId}/history', [EmployeeHistoryController::class, 'store']);
    Route::put('/{employeeId}/history/{historyId}', [EmployeeHistoryController::class, 'update']);
    Route::delete('/{employee}/history/{history}', [EmployeeController::class, 'destroyHistory']);
    Route::get('/{employeeId}/assignment',[EmployeeAssignmentController::class,'index']);
    Route::post('/{employeeId}/assignment',[EmployeeAssignmentController::class,'store']);
    Route::put('/{employeeId}/assignment/{assignment}', [EmployeeAssignmentController::class, 'update']);
    Route::delete('/{employeeId}/assignment/{assignmentId}', [EmployeeAssignmentController::class, 'destroy']);
});
