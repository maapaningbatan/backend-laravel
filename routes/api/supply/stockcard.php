    <?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\Supply\StockCardController;

    Route::prefix('/stock-cards')->group(function () {
        Route::get('/', [StockCardController::class, 'index']);
        Route::get('/purchase-orders-dropdown', [StockCardController::class, 'getPurchaseOrders']);
        Route::get('/{supplyId}', [StockCardController::class, 'show']);
        Route::get('/{id}/ics', [StockCardController::class, 'printICS']);
    });


