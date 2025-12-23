<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Property\PropertyDistributionController;

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/property-issuance-items/{issuanceItemId}/distribution',[PropertyDistributionController::class, 'store']);
    Route::get('/property-issuance-items/{issuanceItemId}/distributions',[PropertyDistributionController::class, 'history']);

});
