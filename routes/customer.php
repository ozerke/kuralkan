<?php

use App\Http\Controllers\Customer\CustomerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Customer router
Route::prefix('panel/customer')->middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/', [CustomerController::class, 'orders'])->name('customer.orders');
    Route::get('/payment-plan', [CustomerController::class, 'paymentPlan'])->name('customer.payment-plan');
    Route::get('/profile', [CustomerController::class, 'profilePage'])->name('customer.profile');
    Route::post('/profile-update', [CustomerController::class, 'profileUpdate'])->name('customer.profile-update');

    Route::prefix('orders')->group(function () {
        Route::get('details/{orderNo}', [CustomerController::class, 'orderDetails'])->name('customer.order-details');
    });
});
