<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\LibraryController\PositionController;
use App\Http\Controllers\LibraryController\RegionController;
use App\Http\Controllers\LibraryController\ClusterController;
use App\Http\Controllers\LibraryController\OfficeController;
use App\Http\Controllers\LibraryController\DivisionController;
use App\Http\Controllers\LibraryController\StatusOfAppointmentController;
use App\Http\Controllers\LibraryController\UserLevelController;

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
Route::get('/positions', [PositionController::class, 'index']);
Route::get('/regions', [RegionController::class, 'index']);
Route::get('/clusters', [ClusterController::class, 'index']);
Route::get('/offices/region/{regionId}', [OfficeController::class, 'getByRegion']);
Route::get('/divisions', [DivisionController::class, 'index']);
Route::get('/divisions/office/{officeId}', [DivisionController::class, 'byOffice']);
Route::get('/status-of-appointments', [StatusOfAppointmentController::class, 'index']);
Route::get('/user-levels', [UserLevelController::class, 'index']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [LoginController::class, 'user']);
    Route::post('/logout', [LoginController::class, 'logout']);
});
