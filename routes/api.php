<?php

use App\Http\Controllers\Property\PropertyCardController;
use App\Http\Controllers\Property\PropertyIssuanceController;
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
use App\Http\Controllers\LibraryController\SupplierController;
use App\Http\Controllers\LibraryController\WarehouseController;
use App\Http\Controllers\LibraryController\SupplyController;
use App\Http\Controllers\LibraryController\UnitController;
use App\Http\Controllers\LibraryController\CategoryController;
use App\Http\Controllers\LibraryController\BrandController;
use App\Http\Controllers\LibraryController\ModelController;
use App\Http\Controllers\Supply\DeliveryController;
use App\Http\Controllers\Supply\StockCardController;
use App\Http\Controllers\LibraryController\ItemTypeController;
use App\Http\Controllers\Property\SemiExCardController;
use App\Http\Controllers\LibraryController\UserController;
use App\Http\Controllers\Supply\RISController;


Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('/users', [UserController::class, 'index']);
Route::apiResource('positions', PositionController::class);

Route::get('/employees', [UserController::class, 'getByOffice']);
Route::get('/employees', [UserController::class, 'getByDivision']);

Route::get('/regions', [RegionController::class, 'index']);
Route::get('/regions/{id}', [RegionController::class, 'show']);

Route::get('/clusters', [ClusterController::class, 'index']);
Route::get('/offices/region/{regionId}', [OfficeController::class, 'getByRegion']);

Route::get('/divisions', [DivisionController::class, 'index']);
Route::get('/divisions/office/{officeId}', [DivisionController::class, 'byOffice']);
Route::get('/divisions/{id}', [DivisionController::class, 'show']);

Route::get('/status-of-appointments', [StatusOfAppointmentController::class, 'index']);
Route::get('/user-levels', [UserLevelController::class, 'index']);
Route::get('/suppliers', [SupplierController::class, 'index']);
Route::get('/warehouses', [WarehouseController::class, 'index']);
Route::get('/supplies', [SupplyController::class, 'index']);
Route::patch('/supplies/{id}/toggle', [SupplyController::class, 'toggleStatus']);

Route::get('/units', [UnitController::class, 'index']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/brands', [BrandController::class, 'index']);
Route::get('/models', [ModelController::class, 'index']);

// SemiExCard
Route::get('/semi-expandable-cards', [SemiExCardController::class, 'index']);
Route::get('/semi-expandable-cards/{id}', [SemiExCardController::class, 'show']);
//PropertyCard
Route::get('/property-cards',[PropertyCardController::class,'index']);
Route::get('/property-cards/{id}',[PropertyCardController::class,'show']);
//Property Issuance
Route::get('/property-issuance',[PropertyIssuanceController::class,'index']);
Route::get('/property-issuance/{id}',[PropertyIssuanceController::class,'show']);
//RIS
Route::get('/ris', [RISController::class, 'index']);
Route::post('/ris', [RISController::class, 'store']);
Route::get('/ris/{id}', [RISController::class, 'show']);
Route::put('/ris/{id}', [RISController::class, 'update']);
Route::delete('/ris/{id}', [RISController::class, 'destroy']);
Route::post('/ris/{id}/approve', [RISController::class, 'approve']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [LoginController::class, 'user']);
    Route::get('/user-profile', [UserController::class, 'profile']);
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/stock-card/{supplyId}', [StockCardController::class, 'show']);
    Route::get('/itemtypes', [ItemTypeController::class, 'index']);




    // Delivery
    Route::get('/delivery/next-code', [DeliveryController::class, 'getNextCode']);
    Route::prefix('delivery')->group(function () {
    Route::get('/', [DeliveryController::class, 'index']);           // GET /delivery
    Route::post('/add', [DeliveryController::class, 'store']);       // POST /delivery/add
    Route::delete('/{id}', [DeliveryController::class, 'destroy']); // DELETE /delivery/{id}
    Route::patch('/edit/{id}', [DeliveryController::class, 'update']); // PATCH /delivery/edit/{id}
    Route::get('/{id}', [DeliveryController::class, 'show']);       // GET /delivery/{id} for edit
    Route::post('/{id}/approve', [DeliveryController::class, 'approve']);




    Route::get('/stock-card/{supplyId}', [StockCardController::class, 'show']);


});
});




