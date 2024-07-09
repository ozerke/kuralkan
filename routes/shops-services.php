<?php

use App\Http\Controllers\Shop\ConsignedProductsController;
use App\Http\Controllers\Shop\ShopController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Shop Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Shop router
Route::prefix('panel/shop')->middleware(['auth', 'role:shop|shop-service'])->group(function () {
    Route::get('/', [ShopController::class, 'orders'])->name('shop.orders');
    Route::get('/settings', [ShopController::class, 'settingsPage'])->name('shop.settings');
    Route::get('/payment-plans', [ShopController::class, 'paymentPlans'])->name('shop.payment-plans');
    Route::get('/consigned-products', [ConsignedProductsController::class, 'consignedProducts'])->name('shop.consigned-products');
    Route::get('/buy-consigned-product/{consignedOrderId}', [ConsignedProductsController::class, 'buyProduct'])->name('shop.consigned-products.buy');

    Route::prefix('orders')->group(function () {
        Route::get('details/{orderNo}', [ShopController::class, 'orderDetails'])->name('shop.order-details');
    });
});
