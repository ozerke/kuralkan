<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\ConfigurationController;
use App\Http\Controllers\Admin\HeroSlidesController;
use App\Http\Controllers\Admin\MenuSectionsController;
use App\Http\Controllers\Admin\Orders\OrdersController;
use App\Http\Controllers\Admin\Products\ColorsController;
use App\Http\Controllers\Admin\Products\SpecificationsController;
use App\Http\Controllers\Admin\Products\VariationsController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\PublicFilesController;
use App\Http\Controllers\Admin\RedirectsController;
use App\Http\Controllers\Admin\Templates\MailTemplateController;
use App\Http\Controllers\Admin\Templates\SMSTemplateController;
use App\Http\Controllers\Admin\Users\UsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Admin router
Route::prefix('panel/admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [AdminController::class, 'index']);
    Route::get('/trigger-job/{jobName}', [AdminController::class, 'triggerManualJob'])->name('manual-job');

    Route::resource('products', ProductsController::class);
    Route::get('/products/{id}/variations', [VariationsController::class, 'index'])->name('variations.index');
    Route::get('/products/{id}/image-gallery', [ProductsController::class, 'editImages'])->name('products.edit-images');
    Route::patch('/products/{id}/update-media', [ProductsController::class, 'updateMedia'])->name('products.update-media');
    Route::post('/products/{id}/delete-media', [ProductsController::class, 'deleteMedia']);
    Route::post('/products/{id}/reorder-media', [ProductsController::class, 'reorderMedia']);
    Route::get('/variations/{id}/edit', [VariationsController::class, 'edit'])->name('variations.edit');
    Route::patch('/variations/{id}/update', [VariationsController::class, 'update'])->name('variations.update');
    Route::post('/variations/upload-media', [VariationsController::class, 'uploadMedia'])->name('variations.upload-media');
    Route::get('/variations/toggle-display/{id}', [VariationsController::class, 'toggleDisplay']);
    Route::post('/variations/{id}/delete-media', [VariationsController::class, 'deleteMedia']);
    Route::post('/variations/{id}/reorder-media', [VariationsController::class, 'reorderMedia']);
    Route::get('/variations/{id}/shop-stocks', [VariationsController::class, 'shopStocksByVariation'])->name('variations.shop-stocks');
    Route::get('/variations/{id}/orders', [VariationsController::class, 'orders'])->name('variations.orders');

    Route::resource('categories', CategoriesController::class);
    Route::post('/categories/update-category/{id}', [CategoriesController::class, 'updateCategory']);
    Route::post('/categories/update-slug/{id}', [CategoriesController::class, 'updateSlug']);
    Route::post('/categories/{id}/update-display-order', [CategoriesController::class, 'updateDisplayOrder']);

    Route::get('/products/{id}/specifications', [SpecificationsController::class, 'index'])->name('specifications.index');
    Route::post('/products/specifications/{id}/update-specification', [SpecificationsController::class, 'updateSpecification']);
    Route::post('/products/specifications/{id}/update-value', [SpecificationsController::class, 'updateValue']);

    Route::get('/toggle-new-product/{id}', [ProductsController::class, 'toggleNewProduct']);
    Route::get('/toggle-display/{id}', [ProductsController::class, 'toggleDisplay']);

    Route::resource('users', UsersController::class);
    Route::get('/users/{id}/shop-stocks', [UsersController::class, 'shopVariationStocks'])->name('users.shop-stocks');
    Route::get('/users/{id}/payments', [UsersController::class, 'userPayments'])->name('users.payments');
    Route::get('/users/{id}/bond-payments', [UsersController::class, 'bondPayments'])->name('users.bond-payments');
    Route::get('/users/{id}/orders', [UsersController::class, 'orders'])->name('users.orders');

    Route::resource('hero-slides', HeroSlidesController::class);
    Route::resource('colors', ColorsController::class);
    Route::delete('/colors/{id}/delete-image', [ColorsController::class, 'deleteImage'])->name('colors.deleteImage');

    Route::resource('orders', OrdersController::class);
    Route::get('/order/status-history/{orderId}', [OrdersController::class, 'statusHistory'])->name('orders.status-history');
    Route::get('/order/bond-payments/{orderId}', [OrdersController::class, 'bondPaymentsList'])->name('orders.bond-payments-list');
    Route::get('/order/details/{orderId}', [OrdersController::class, 'orderDetails'])->name('orders.details');
    Route::post('/order/cancel-payment/{paymentId}', [OrdersController::class, 'cancelPayment'])->name('orders.cancel-payment');
    Route::post('/order/reject-notary-document/{orderId}', [OrdersController::class, 'rejectNotaryDocument'])->name('orders.reject-notary-document');

    Route::get('/configuration/cache-flush', [ConfigurationController::class, 'flushCache'])->name('configuration.cache-flush');

    Route::resource('configuration', ConfigurationController::class);
    Route::resource('menu-sections', MenuSectionsController::class);
    Route::resource('public-files', PublicFilesController::class);
    Route::resource('redirects', RedirectsController::class);
    Route::resource('campaigns', CampaignController::class);
    Route::resource('templates/sms', SMSTemplateController::class)->names('templates.sms');
    Route::resource('templates/mail', MailTemplateController::class)->names('templates.mail');
});
