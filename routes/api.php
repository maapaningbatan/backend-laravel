<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Library\ArticleController;
use App\Http\Controllers\Library\BrandController;
use App\Http\Controllers\Library\CategoryController;
use App\Http\Controllers\Library\ClusterController;
use App\Http\Controllers\Library\DivisionController;
use App\Http\Controllers\Library\FundController;
use App\Http\Controllers\Library\ItemTypeController;
use App\Http\Controllers\Library\ModelController;
use App\Http\Controllers\Library\OfficeController;
use App\Http\Controllers\Library\PositionController;
use App\Http\Controllers\Library\RegionController;
use App\Http\Controllers\Library\StatusOfAppointmentController;
use App\Http\Controllers\Library\SupplierController;
use App\Http\Controllers\Library\SupplyController;
use App\Http\Controllers\Library\UnitController;
use App\Http\Controllers\Library\UserController;
use App\Http\Controllers\Library\UserLevelController;
use App\Http\Controllers\Library\WarehouseController;
use App\Http\Controllers\Library\LibCenterController;
use App\Http\Controllers\Property\PropertyCardController;
use App\Http\Controllers\Property\PropertyIssuanceController;
use App\Http\Controllers\Property\SemiExCardController;
use App\Http\Controllers\Supply\DeliveryController;
use App\Http\Controllers\Supply\RISController;
use App\Http\Controllers\Supply\StockCardController;
use Illuminate\Support\Facades\Route;

// Auth
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login'])->name('login');
// User
Route::get('/users', [UserController::class, 'index']);
Route::patch('/users/{id}/toggle', [UserController::class, 'toggleActivation']);

Route::get('/employees', [UserController::class, 'getByOffice']);
Route::get('/employees', [UserController::class, 'getByDivision']);
// Position
Route::apiResource('positions', PositionController::class);
// Region
Route::get('/regions', [RegionController::class, 'index']);
Route::get('/regions/{id}', [RegionController::class, 'show']);
// Cluster
Route::get('/clusters', [ClusterController::class, 'index']);
// Office
Route::get('/offices/region/{regionId}', [OfficeController::class, 'getByRegion']);
Route::get('/offices/region/{regionId}', [OfficeController::class, 'getByRegion']);
// Division
Route::get('/divisions', [DivisionController::class, 'index']);
Route::get('/divisions/office/{officeId}', [DivisionController::class, 'byOffice']);
Route::get('/divisions/{id}', [DivisionController::class, 'show']);
// Centers
Route::apiResource('centers', LibCenterController::class);

Route::get('/status-of-appointments', [StatusOfAppointmentController::class, 'index']);
Route::get('/user-levels', [UserLevelController::class, 'index']);
Route::get('/suppliers', [SupplierController::class, 'index']);
Route::get('/warehouses', [WarehouseController::class, 'index']);
Route::get('/supplies', [SupplyController::class, 'index']);
Route::patch('/supplies/{id}/toggle', [SupplyController::class, 'toggleStatus']);
Route::get('/articles', [ArticleController::class, 'index']);

Route::get('/units', [UnitController::class, 'index']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/brands', [BrandController::class, 'index']);
Route::get('/models', [ModelController::class, 'index']);
Route::get('/funds', [FundController::class, 'index']);

// SemiExCard
Route::get('/semi-expandable-cards', [SemiExCardController::class, 'index']);
Route::get('/semi-expandable-cards/{id}', [SemiExCardController::class, 'show']);
// PropertyCard
Route::get('/property-cards', [PropertyCardController::class, 'index']);
Route::get('/property-cards/{id}', [PropertyCardController::class, 'show']);
// Property Issuance
Route::get('/property-issuance', [PropertyIssuanceController::class, 'index']);
Route::get('/property-issuance/{id}', [PropertyIssuanceController::class, 'show']);
// RIS
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
