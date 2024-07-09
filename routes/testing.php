<?php

use App\Http\Controllers\Orders\SoapSendOrderController;
use App\Models\OrderPayment;
use App\Services\CreditCardGateway;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use App\Utils\SoapUtils;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Testing Routes
|--------------------------------------------------------------------------
|
| Only availble in development mode
|
*/

Route::prefix('testing')->group(function () {
    // Route::get('/validate-payment-hash/{id}', function ($id) {
    //     $service = new CreditCardGateway();

    //     $orderPayment = OrderPayment::withTrashed()->find($id);

    //     if (!$orderPayment) {
    //         return 'Payment not found';
    //     }

    //     $paymentResponse = json_decode($orderPayment->payment_gateway_response);

    //     return [
    //         'BTResponse' => $paymentResponse,
    //         'order' => $orderPayment->order,
    //         'hashValidation' => $service->validateIncomingHash($paymentResponse, $orderPayment->order, true)
    //     ];
    // });

    // Route::get('test', function () {
    //     $service = new SoapSendOrderController();

    //     $stock_code = 'TMB8916ND401';

    //     $downPayments = $service->downPayments($stock_code);

    //     $productPlans = collect();

    //     if (!$downPayments) {
    //         LoggerService::logInfo(LogChannelsEnum::UpdatePaymentPlansForProduct, 'No $downPayments', ['stock_code' => $stock_code]);

    //         return;
    //     }

    //     if (!property_exists($downPayments, 'ROW')) {
    //         LoggerService::logInfo(LogChannelsEnum::UpdatePaymentPlansForProduct, 'No ROW', ['stock_code' => $stock_code]);

    //         return;
    //     }

    //     $downPayments = SoapUtils::parseRow($downPayments, function ($item) {
    //         return [
    //             'amount' => $item['PESINATTUTARI'],
    //         ];
    //     });

    //     foreach ($downPayments as $downPayment) {
    //         $installments = $service->installmentOptions(join(',', [$stock_code, $downPayment['amount']]));

    //         if (!$installments) {
    //             LoggerService::logInfo(LogChannelsEnum::UpdatePaymentPlansForProduct, 'No $installments', ['stock_code' => $stock_code, 'dp' => $downPayment['amount']]);

    //             continue;
    //         }

    //         if (!property_exists($installments, 'ROW')) {
    //             LoggerService::logInfo(LogChannelsEnum::UpdatePaymentPlansForProduct, 'No ROW for installments', ['stock_code' => $stock_code, 'dp' => $downPayment['amount']]);

    //             continue;
    //         }

    //         $installmentsObject = collect($installments)['ROW'];

    //         $installmentsObject = json_decode(json_encode($installmentsObject), true, flags: JSON_OBJECT_AS_ARRAY);

    //         $isSingleEntity = array_key_exists('SENETSAYISI', $installmentsObject) || array_key_exists('SENETTUTARI', $installmentsObject);

    //         $installments = SoapUtils::parseRow($installments, function ($item) {
    //             return [
    //                 'installments' => $item['SENETSAYISI'],
    //                 'monthly_payment' => $item['SENETTUTARI']
    //             ];
    //         }, $isSingleEntity);

    //         if ($isSingleEntity) {
    //             $productPlans->push([
    //                 'down_payment' => $downPayment['amount'],
    //                 'installments' => [$installments]
    //             ]);
    //         } else {
    //             $productPlans->push([
    //                 'down_payment' => $downPayment['amount'],
    //                 'installments' => $installments
    //             ]);
    //         }
    //     }

    //     return $productPlans;

    //     // foreach ($productPlans as $plan) {
    //     //     $downPayment = $this->product->downPayments()->create([
    //     //         'amount' => $plan['down_payment']
    //     //     ]);

    //     //     if (isset($plan['installments'])) {
    //     //         foreach ($plan['installments'] as $planInstallment) {
    //     //             $downPayment->installmentOptions()->create([
    //     //                 'installments' => $planInstallment['installments'],
    //     //                 'monthly_payment' => $planInstallment['monthly_payment']
    //     //             ]);
    //     //         }
    //     //     }
    //     // }
    // });
});
