<?php

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\OpenDataController;
use App\Http\Controllers\API\ERPApiController;
use App\Http\Controllers\API\ChatBotApiController;
use App\Models\OrderPayment;
use App\Services\CreditCardGateway;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('erpBasicAuth')->group(function () {
    Route::get('/orders/pending', [ERPApiController::class, 'getPendingOrders']);
    Route::post('/orders/update', [ERPApiController::class, 'updateOrder']);

    Route::get('/payments/list', [ERPApiController::class, 'getPaymentList']);
    Route::post('/payments/update', [ERPApiController::class, 'updatePayment']);

    Route::post('/products/update', [ERPApiController::class, 'updateProducts']);
    Route::post('/products/technical-specs', [ERPApiController::class, 'updateTechnicalSpecs']);

    Route::post('/products/consigned', [ERPApiController::class, 'updateConsignedProducts']);
    Route::get('/products/consigned/list', [ERPApiController::class, 'getConsignedProductsList']);

    Route::post('/ebonds/update', [ERPApiController::class, 'updateEbonds']);
    Route::get('/ebonds/list', [ERPApiController::class, 'getEbondsList']);

    Route::post('/shop-services/update', [ERPApiController::class, 'updateShopServices']);

    Route::post('/stocks/update', [ERPApiController::class, 'updateStocks']);

    Route::get('/legal-docs/{document}', [ApiController::class, 'getLegalDocument']);
    Route::get('/notary-docs/{document}', [ApiController::class, 'getNotaryDocument']);

    Route::get('/order', [ChatBotApiController::class, 'getOrderInfo']);
    Route::get('/product/price', [ChatBotApiController::class, 'getProductPrice']);
});

Route::middleware('erpBasicAuth')->get('/validate-payment-hash/{id}', function ($id) {
    $service = new CreditCardGateway();

    $orderPayment = OrderPayment::withTrashed()->find($id);

    if (!$orderPayment) {
        return 'Payment not found';
    }

    $paymentResponse = json_decode($orderPayment->payment_gateway_response);

    return [
        'BTResponse' => $paymentResponse,
        'order' => $orderPayment->order,
        'hashValidation' => $service->validateIncomingHash($paymentResponse, $orderPayment->order, true)
    ];
});

Route::get('/products', [OpenDataController::class, 'getProducts']);
Route::get('/product', [OpenDataController::class, 'getProduct']);

Route::post('/get-installments', [ApiController::class, 'getInstallmentsByCreditCardDigits']);

Route::get('/search', [ApiController::class, 'searchForProducts']);
