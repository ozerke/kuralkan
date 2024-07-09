<?php

use App\Http\Controllers\DataController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Orders\LegalRegistrationController;
use App\Http\Controllers\Orders\OrderController;
use App\Http\Controllers\Payment\OrderPaymentsController;
use App\Http\Controllers\Payment\SalesAgreementController;
use App\Http\Controllers\ProductDetailsController;
use App\Http\Controllers\RouterController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('google20def872811c3be8.html', function () {
    return File::get(public_path('google20def872811c3be8.html'));
});

Route::get('/sitemap.xml', function () {
    $sitemapPath = public_path('sitemap.xml');

    return response()->file($sitemapPath);
});

Route::get('/change-language/{locale}', function ($locale) {
    if (!in_array($locale, config('app.available_locales'))) {
        abort(400);
    }

    app()->setLocale($locale);
    session()->put('locale', $locale);

    return redirect()->back();
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/motosiklet-satis-noktalari', [HomeController::class, 'salesPoints'])->name('salesPoints');
Route::get('/motosiklet-teslimat-noktalari', [HomeController::class, 'servicePoints'])->name('servicePoints');
Route::get('/get-districts-sales', [HomeController::class, 'getDistrictsForSales'])->name('getDistrictsForSales');
Route::get('/get-districts-services', [HomeController::class, 'getDistrictsForServices'])->name('getDistrictsForServices');
Route::post('/submit-newsletter', [HomeController::class, 'newsletterSubmit'])->name('submit-newsletter');
Route::get('/musteri-iletisim-formu', [HomeController::class, 'musteriIletisimFormu'])->name('musteri-iletisim-formu');

Route::get('/search', [ProductDetailsController::class, 'searchPage'])->name('searchForProducts');

Route::prefix('otp')->group(function () {
    Route::post('request', [VerificationController::class, 'requestVerificationForPhone'])->name('otp.request');
    Route::post('verify', [VerificationController::class, 'verifyCode'])->name('otp.verify');
});

Route::prefix('customer-verify')->middleware(['auth', 'role:shop|shop-service'])->group(function () {
    Route::post('check-email', [VerificationController::class, 'checkUniqueEmail'])->name('customer-verify.check-email');
    Route::post('check-phone', [VerificationController::class, 'checkUniquePhone'])->name('customer-verify.check-phone');
    Route::post('otp-request', [VerificationController::class, 'requestVerificationFromShop'])->name('customer-verify.otp-request');
    Route::post('otp-verify', [VerificationController::class, 'verifyCodeFromShop'])->name('customer-verify.otp-verify');
});

Route::prefix('profile-verify')->middleware(['auth'])->group(function () {
    Route::post('otp', [VerificationController::class, 'requestProfileVerification'])->name('profile-otp.request');
    Route::post('verify', [VerificationController::class, 'verifyProfileCode'])->name('profile-otp.verify');
    Route::post('password-update', [VerificationController::class, 'passwordUpdate'])->name('profile.password-update');
});


// 20240128 - OE - Footer Pages
Route::get('/iletisim', [HomeController::class, 'iletisim'])->name('iletisim');
Route::get('/teslimat-kosullari', [HomeController::class, 'teslimatKosullari'])->name('teslimatKosullari');
Route::get('/garanti-ve-iade-kosullari', [HomeController::class, 'garantiVeIadeKosullari'])->name('garantiVeIadeKosullari');
Route::get('/gizlilik-ve-guvenlik', [HomeController::class, 'gizlilikVeGuvenlik'])->name('gizlilikVeGuvenlik');
Route::get('/hakkimizda', [HomeController::class, 'hakkimizda'])->name('hakkimizda');
Route::get('/uyelik-sozlesmesi', [HomeController::class, 'uyelikSozlesmesi'])->name('uyelikSozlesmesi');
Route::get('/sikca-sorulan-sorular', [HomeController::class, 'sikcaSorulanSorular'])->name('sikcaSorulanSorular');
// 20240128 - OE - Footer Pages

Route::post('/product/quick-buy', [ProductDetailsController::class, 'quickBuy'])->name('quick-buy');
Route::get('/sepetim', [OrderController::class, 'index'])->name('cart');
Route::get('/clear-cart', [OrderController::class, 'clearCart'])->name('clear-cart');
Route::middleware('auth')->group(function () {
    Route::get('/invoice-information', [OrderController::class, 'invoiceInformation'])->name('invoice-information');
    Route::post('/submit-order-information', [OrderController::class, 'submitOrderInformation'])->name('submit-order-information');
    Route::get('/payment/{orderNo}', [OrderPaymentsController::class, 'orderPayment'])->name('order-payment')->middleware('disable.cache');
    Route::get('/redirect-to-payment/{orderNo}', [OrderPaymentsController::class, 'redirectToPaymentPage'])->name('redirect-to-payment');
    Route::post('/process-payment/{orderNo}', [OrderPaymentsController::class, 'processPayment'])->name('process-payment');
    Route::get('/payment/thank-you/{orderNo}', [OrderPaymentsController::class, 'orderPaidIndex'])->name('thank-you');
    Route::post('/cancel-payment/{paymentRefNo}', [OrderPaymentsController::class, 'cancelPayment'])->name('cancel-payment');
    Route::get('/remote-sales-pdf/{orderNo}', [OrderController::class, 'retrieveRemoteSalesPdf'])->name('remote-sales-pdf');

    Route::get('/order-processing/{orderNo}', [OrderController::class, 'orderProcessingPage'])->name('order-processing');
    Route::post('/check-order-processing', [OrderController::class, 'checkOrderProcessing'])->name('check-order-processing');

    Route::get('/legal-registration/{orderNo}', [LegalRegistrationController::class, 'legalForm'])->name('legal-registration-form');
    Route::post('/legal-registration-submit/{orderNo}', [LegalRegistrationController::class, 'submitForm'])->name('legal-registration-form.submit');

    Route::prefix('sales-agreement')->group(function () {
        Route::get('/payment-plan/{orderNo}', [SalesAgreementController::class, 'paymentPlanPage'])->name('sales-agreements.payment-plan');
        Route::post('/select-plan/{orderNo}', [SalesAgreementController::class, 'selectPlan'])->name('sales-agreements.select-plan');
        Route::get('/check-findeks-verification/{orderNo}', [SalesAgreementController::class, 'checkFindeksVerification'])->name('sales-agreements.check-findeks-verification');

        Route::get('/application-fee/{orderNo}', [SalesAgreementController::class, 'applicationFeePage'])->name('sales-agreements.application-fee');
        Route::post('/process-fee/{orderNo}', [SalesAgreementController::class, 'processFeePayment'])->name('sales-agreements.process-fee-payment');

        Route::get('/findeks-sms-pin/{orderNo}', [SalesAgreementController::class, 'findeksSmsPinPage'])->name('sales-agreements.findeks-sms-pin');
        Route::post('/findeks-sms-pin-check/{orderNo}', [SalesAgreementController::class, 'checkFindeksPin'])->name('sales-agreements.check-findeks-pin');

        Route::get('/retry-later/{orderNo}', [SalesAgreementController::class, 'retryLater'])->name('sales-agreements.retry-later');
        Route::get('/application-rejected/{orderNo}', [SalesAgreementController::class, 'applicationRejected'])->name('sales-agreements.application-rejected');

        Route::get('/payment-collect/{orderNo}', [SalesAgreementController::class, 'collectDownPayment'])->name('sales-agreements.collect-down-payment');
        Route::get('/thank-you/{orderNo}', [SalesAgreementController::class, 'thankYouPage'])->name('sales-agreements.thank-you');
        Route::post('/check-document', [SalesAgreementController::class, 'checkDocumentStatus'])->name('check-document-status');

        Route::get('/processing/{orderNo}', [SalesAgreementController::class, 'proccessingPage'])->name('sales-agreements.processing');
        Route::post('/check-processing-status', [SalesAgreementController::class, 'checkProcessingStatus'])->name('check-processing-status');

        Route::post('/upload-notary-document/{orderNo}/{type}', [SalesAgreementController::class, 'uploadNotaryDocument'])->name('upload-notary-document');

        Route::get('/bond-payment/{orderNo}', [SalesAgreementController::class, 'bondPaymentPage'])->name('sales-agreements.bond-payment-page');
        Route::post('/process-bond-payment/{orderNo}', [SalesAgreementController::class, 'processBondPayment'])->name('sales-agreements.process-bond-payment');
        Route::get('/bond-payments-list/{orderNo}', [SalesAgreementController::class, 'bondPaymentList'])->name('sales-agreements.bond-payments-list');
    });
});

Route::get('/panel', [RouterController::class, 'resolveRedirectByRole'])->middleware(['auth'])->name('panel');

Route::get('/data/cities/{id}', [DataController::class, 'getCitiesFromCountry']);
Route::get('/data/districts/{id}', [DataController::class, 'getDistrictsFromCity']);
Route::get('/data/delivery-districts/{id}', [DataController::class, 'getDeliveryDistrictsFromCity']);
Route::get('/data/service-points/{id}', [DataController::class, 'getServicePointsFromDistrict']);
Route::post('/data/check-national-id', [DataController::class, 'checkNationalId']);
Route::post('/data/get-stocks', [ProductDetailsController::class, 'getStocksDataForProduct']);

Route::middleware('checkPaymentOrigin')->group(function () {
    Route::post('/handle-payment/{orderRefNo}', [OrderPaymentsController::class, 'handleCreditCardResponse']);
    Route::post('/handle-fee-payment/{orderRefNo}', [SalesAgreementController::class, 'handleFeeCreditCardResponse']);
    Route::post('/handle-bond-payment/{orderRefNo}', [SalesAgreementController::class, 'handleBondCreditCardResponse']);
});

require __DIR__ . '/admin.php';
require __DIR__ . '/shops-services.php';
require __DIR__ . '/customer.php';
require __DIR__ . '/auth.php';

if (!app()->environment('production')) {
    require __DIR__ . '/testing.php';
}

Route::get('/{slug}', [ProductDetailsController::class, 'resolvePageItemBySlug'])->name('item-by-slug');
