<?php

namespace App\Http\Controllers\Payment;

use App\Handlers\SalesAgreementHandler;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Orders\SoapSendOrderController;
use App\Http\Requests\Payments\BondPaymentRequest;
use App\Http\Requests\Payments\FeePaymentRequest;
use App\Jobs\Orders\SalesAgreements\CheckFindeksPinJob;
use App\Jobs\Orders\SalesAgreements\SalesAgreementDocumentJob;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Configuration;
use App\Models\Order;
use App\Models\SalesAgreement;
use App\Models\User;
use App\Services\CreditCardGateway;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalesAgreementController extends Controller
{
    public function paymentPlanPage(Request $request, $orderNo)
    {
        try {
            $order = auth()->user()->getOrderForUser($orderNo, ['salesAgreement', 'productVariation.product.downPayments']);

            if (!$order || $order->isCancelled() || !$order->isSalesAgreementOrder() || !$order->erp_order_id) {
                return redirect()->route('home');
            }

            $salesAgreement = $order->salesAgreement;

            if ($salesAgreement) {
                $handler = new SalesAgreementHandler($salesAgreement);

                return $handler->navigateUserToStage();
            }

            $paidState = $order->getOrderPaymentsState();

            $downPayments = $order->productVariation->product->downPayments;

            $installments = null;

            $downPayment = $request->input('down_payment_amount');

            if (!empty($downPayment)) {

                $downPaymentInstance = $downPayments->where('amount', $downPayment)->first();

                if ($downPaymentInstance) {
                    $installments = $downPaymentInstance->installmentOptions;
                }
            }

            $locale = app()->getLocale();

            $explanation = Configuration::getSalesAgreementExplanation($locale);

            return view('home.orders.sales-agreements.payment-plan')->with([
                'order' => $order,
                'paidState' => $paidState,
                'downPayments' => $downPayments,
                'installments' => $installments,
                'explanation' => $explanation ? $explanation->value : null,
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'paymentPlanPage', ['e' => $e, 'order_no' => $orderNo]);

            return redirect()->route('home')->with('error', 'Error occurred. Contact the support.');
        }
    }

    public function selectPlan(Request $request, $orderNo)
    {
        try {
            $order = auth()->user()->getOrderForUser($orderNo, ['salesAgreement', 'productVariation.product.downPayments']);

            if (!$order || $order->isCancelled() || !$order->isSalesAgreementOrder() || !$order->erp_order_id) {
                return redirect()->route('home');
            }

            $salesAgreement = $order->salesAgreement;

            if ($salesAgreement) {
                $handler = new SalesAgreementHandler($salesAgreement);

                return $handler->navigateUserToStage();
            }

            $downPayment = $request->input('selected-down-payment');
            $selectedInstallments = $request->input('selected-installments');

            $downPayments = $order->productVariation->product->downPayments;

            $installments = null;

            if (!empty($downPayment)) {

                $downPaymentInstance = $downPayments->where('amount', $downPayment)->first();

                if ($downPaymentInstance) {
                    $installments = $downPaymentInstance->installmentOptions;
                }
            }

            $selectedInstallment = null;

            if (!empty($installments)) {
                $selectedInstallment = $installments->where('installments', $selectedInstallments)->first();
            }

            if (!$selectedInstallment) {
                LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'selectPlan: Selected installment not found', ['installment' => $selectedInstallments, 'order_no' => $orderNo]);
                return redirect()->route('home')->with('error', 'Error occurred. Contact the support.');
            }

            $totalAmount = (int) $selectedInstallments * (float) $selectedInstallment['monthly_payment'];

            $totalAmount += (float) $downPayment;

            $data = [
                'down_payment_amount' => $downPayment,
                'number_of_installments' => $selectedInstallments,
                'agreement_total_amount' => $totalAmount,
                'monthly_payment' => (float) $selectedInstallment['monthly_payment'],
                'is_new_agreement' => true
            ];

            $salesAgreement = $order->salesAgreement()->create($data);

            if ($salesAgreement) {
                return redirect()->route('sales-agreements.application-fee', ['orderNo' => $orderNo]);
            }

            return redirect()->route('home')->with('error', 'Error occurred. Contact the support.');
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'selectPlan', ['e' => $e, 'order_no' => $orderNo]);

            return redirect()->route('home')->with('error', 'Error occurred. Contact the support.');
        }
    }

    public function checkFindeksVerification(Request $request, $orderNo)
    {
        try {
            $order = auth()->user()->getOrderForUser($orderNo, ['invoiceUser', 'salesAgreement']);

            if (!$order || $order->isCancelled() || !$order->isSalesAgreementOrder() || !$order->erp_order_id) {
                return redirect()->route('home');
            }

            if ($order->invoiceUser->is_findeks_verified) {
                return back()->with('success', __('web.findeks-verification-success'));
            }

            $nationalId = $order->invoiceUser->national_id;

            if (!$nationalId) {
                return back()->with('error', __('web.national-id-missing'));
            }

            $service = new SoapSendOrderController();

            $verification = $service->checkFindeksRegistration($nationalId);

            $isVerified = $verification === "1";

            $order->invoiceUser->update([
                'is_findeks_verified' => $isVerified
            ]);

            if ($isVerified) {
                return back()->with('success', __('web.findeks-verification-success'));
            } else {
                return back()->with('error', __('web.findeks-verification-error'));
            }
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'checkFindeksVerification', ['e' => $e, 'order_no' => $orderNo]);

            return back()->with('error', 'Error occurred. Contact the support.');
        }
    }

    public function redirectToStage(Request $request, $orderNo)
    {
        try {
            $order = auth()->user()->getOrderForUser($orderNo, ['salesAgreement']);

            if (!$order || $order->isCancelled() || !$order->isSalesAgreementOrder() || !$order->erp_order_id) {
                return redirect()->route('home');
            }

            $handler = new SalesAgreementHandler($order->salesAgreement);

            return $handler->navigateUserToStage();
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'redirectToStage', ['e' => $e, 'order_no' => $orderNo]);

            return redirect()->route('home')->with('error', 'Error occurred. Contact the support.');
        }
    }

    public function applicationFeePage(Request $request, $orderNo)
    {
        try {
            $order = auth()->user()->getOrderForUser($orderNo, ['invoiceUser', 'salesAgreement']);

            if (!$order || $order->isCancelled() || !$order->isSalesAgreementOrder() || !$order->erp_order_id) {
                return redirect()->route('home');
            }

            $fee = Configuration::getApplicationFee();

            if (!$fee) {
                return redirect()->route('home')->with('error', 'Error occurred. Application fee not configured.');
            }

            if ($order->salesAgreement->application_fee_payment_id) {
                $handler = new SalesAgreementHandler($order->salesAgreement);

                return $handler->navigateUserToStage();
            }

            return view('home.orders.sales-agreements.application-fee')->with([
                'order' => $order,
                'fee' => $fee->value,
                'salesAgreement' => $order->salesAgreement
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'applicationFeePage', ['e' => $e, 'order_no' => $orderNo]);

            return redirect()->route('home')->with('error', 'Error occurred. Contact the support.');
        }
    }

    public function processFeePayment(FeePaymentRequest $request, $orderNo)
    {
        DB::beginTransaction();

        try {
            $order = auth()->user()->getOrderForUser($orderNo, ['invoiceUser', 'salesAgreement']);

            if (!$order || $order->isCancelled() || !$order->erp_order_id) {
                DB::rollBack();
                return back()->with('error', __('web.order-does-not-exist'));
            }

            $data = $request->validated();

            $ccName = $data['name'] ?? null;
            $ccNumber = $data['number'] ?? null;
            $ccExpiry = $data['expiry'] ?? null;
            $ccCvc = $data['cvc'] ?? null;

            $orderDate = $order->created_at->format('Y-m-d H:i:s');

            $fee = Configuration::getApplicationFee();

            if (strlen($ccNumber) < 16) {
                DB::rollBack();
                return back()->with('error', __('validation.custom.number.size'));
            }

            $gateway = new CreditCardGateway();

            $expiryDate = explode('/', $ccExpiry);
            $year = strlen($expiryDate[1]) > 2 ? $expiryDate[1] : "20" . $expiryDate[1];
            $month = $expiryDate[0];

            $response = $gateway->sendFeePayment(auth()->user()->id, $ccName, $ccNumber, $ccCvc, $month, $year, $order->order_no, $orderDate, $fee->value, $order->invoice_user_id, $request->ip());

            if (in_array($response['StatusCode'], [332, 601])) {
                DB::commit();
                return $response['Form3DContent'];
            }

            LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'processFeePayment: Fee payment error in BT response', ['response' => $response, 'order_no' => $orderNo]);

            DB::rollBack();

            return back()->with('error', CreditCardGateway::translateStatusToError($response['StatusCode'], app()->getLocale()));
        } catch (Exception $e) {
            DB::rollBack();

            LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'processFeePayment', ['e' => $e, 'order_no' => $orderNo]);

            return redirect()->route('home')->with('error', 'Error occurred. Contact the support.');
        }
    }

    public function handleFeeCreditCardResponse(Request $request, $orderNo)
    {
        try {
            $lang = $request->input('app_lang');

            if (!empty($lang)) {
                app()->setLocale($lang);
            }

            $jsonContent = $request->getContent();

            $response = str_replace("jsonData=", "", $jsonContent);

            $response = json_decode($response);

            $order = Order::where('order_no', $orderNo)->first();

            if (!$order) {
                return redirect()->route('home')->with('error', 'Error occurred. Contact the support.');
            }

            $isValidHash = (new CreditCardGateway)->validateIncomingHash($response, $order);

            if (!$isValidHash) {
                LoggerService::logError(LogChannelsEnum::ApplicationOrdering, 'Incorrect hash sent', ['response' => $response, 'order_id' => $order->id, 'debug' => (new CreditCardGateway)->validateIncomingHash($response, $order, true)]);

                return redirect()->route('home')->with('error', CreditCardGateway::translateStatusToError($response->StatusCode, app()->getLocale()));
            } else {
                LoggerService::logSuccess(LogChannelsEnum::ApplicationOrdering, 'Hash validated successfully', ['response' => $response, 'order_id' => $order->id]);
            }

            if (!in_array($response->StatusCode, [332, 601])) {
                $orderRefNo = $response->OrderRefNo;

                $userId = explode("-", $orderRefNo);

                if (!auth()->user()) {
                    $user = User::where('id', $userId[1])->first();

                    if ($user) {
                        Auth::login($user);
                    }
                }

                $bank = Bank::where('vpos_bank_code', $response->VPosBankCode)->first();

                $bankAccountId = null;

                if ($bank) {
                    $bankAccount = BankAccount::where('bank_id', $bank->id)->first();
                    $bankAccountId = !($bankAccount) ? null : $bankAccount->id;
                } else {
                    LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'handleFeeCreditCardResponse: Bank not found', ['vpos' => $response->VPosBankCode, 'order_no' => $orderNo]);
                }

                $order->orderPayments()->create([
                    'payment_amount' => floatval($response->Amount),
                    'payment_type' => 'K',
                    'bank_account_id' => $bankAccountId,
                    'approved_by_erp' => 'N',
                    'failed' => true,
                    'number_of_installments' => $response->Installment,
                    'collected_payment' => floatval($response->Amount),
                    'description' => 'Application fee payment failed',
                    'payment_ref_no' => $orderNo . '-failed-fee',
                    'user_id' => $order->invoice_user_id,
                    'payment_gateway_response' => json_encode($response),
                    'is_fee_payment' => true
                ]);

                return redirect()->route('sales-agreements.application-fee', ['orderNo' => $order->order_no])->with('error', CreditCardGateway::translateStatusToError($response->StatusCode, app()->getLocale()));
            }

            $bankAccountId = null;

            $bank = Bank::where('vpos_bank_code', $response->VPosBankCode)->first();

            if ($bank) {
                $bankAccount = BankAccount::where('bank_id', $bank->id)->first();
                $bankAccountId = !($bankAccount) ? null : $bankAccount->id;
            } else {
                LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'handleFeeCreditCardResponse: Bank not found', ['vpos' => $response->VPosBankCode, 'order_no' => $orderNo]);
            }

            $orderRefNo = $response->OrderRefNo;
            $userId = explode("-", $orderRefNo);

            if (!auth()->user()) {
                $user = User::where('id', $userId[1])->first();

                if ($user) {
                    Auth::login($user);
                }
            }

            if ($order->orderPayments()->where('payment_ref_no', $orderNo . $response->PaymentID)->exists()) {
                return redirect()->route('sales-agreements.application-fee', ['orderNo' => $order->order_no])->with('success', __('web.fee-payment-exists'));
            }

            $feePayment = $order->orderPayments()->create([
                'payment_amount' => floatval($response->Amount),
                'payment_type' => 'K',
                'bank_account_id' => $bankAccountId,
                'approved_by_erp' => 'Y',
                'number_of_installments' => $response->Installment,
                'collected_payment' => floatval($response->Amount),
                'description' => 'Application fee',
                'payment_ref_no' => $orderNo . $response->PaymentID,
                'user_id' => $order->invoice_user_id,
                'payment_gateway_response' => json_encode($response),
                'is_fee_payment' => true
            ]);

            if (!$feePayment) {
                return redirect()->route('sales-agreements.application-fee', ['orderNo' => $order->order_no])->with('error', 'Error occured. Contant the support.');
            }

            $order->salesAgreement->update([
                'application_fee_payment_id' => $feePayment->id
            ]);

            return redirect()->route('sales-agreements.application-fee', ['orderNo' => $order->order_no])->with('success', __('web.fee-payment-success'));
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'handleFeeCreditCardResponse', ['e' => $e, 'order_no' => $orderNo]);

            return redirect()->route('home')->with('error', 'Error occurred. Contact the support.');
        }
    }

    public function applicationRejected(Request $request, $orderNo)
    {
        try {
            $order = auth()->user()->getOrderForUser($orderNo, ['salesAgreement']);

            if (!$order) {
                return redirect()->route('home');
            }

            if ($order->salesAgreement && !$order->salesAgreement->isDeclined()) {
                return redirect()->route('home');
            }

            return view('home.orders.sales-agreements.rejected')->with([
                'order' => $order,
                'salesAgreement' => $order->salesAgreement
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'applicationRejected', ['e' => $e, 'order_no' => $orderNo]);

            return redirect()->route('panel')->with('error', 'Error occurred. Contact the support.');
        }
    }

    public function retryLater(Request $request, $orderNo)
    {
        try {
            $order = auth()->user()->getOrderForUser($orderNo, ['salesAgreement']);

            if (!$order || $order->isCancelled() || !$order->isSalesAgreementOrder() || !$order->erp_order_id) {
                return redirect()->route('home');
            }

            if ($order->salesAgreement && $order->salesAgreement->isDeclined()) {
                return redirect()->route('home');
            }

            $order->salesAgreement->update([
                'retry_count' => 1
            ]);

            return view('home.orders.sales-agreements.retry-later')->with([
                'order' => $order,
                'salesAgreement' => $order->salesAgreement
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'retryLater', ['e' => $e, 'order_no' => $orderNo]);

            return redirect()->route('panel')->with('error', 'Error occurred. Contact the support.');
        }
    }

    public function findeksSmsPinPage(Request $request, $orderNo)
    {
        try {
            $order = auth()->user()->getOrderForUser($orderNo, ['salesAgreement']);

            if (!$order) {
                return redirect()->route('home');
            }

            if ($order->salesAgreement && $order->salesAgreement->isDeclined()) {
                return redirect()->route('home');
            }

            if ($order->salesAgreement->stage !== SalesAgreement::STAGES['sms_pin_pending']) {
                $handler = new SalesAgreementHandler($order->salesAgreement);

                return $handler->navigateUserToStage();
            }

            return view('home.orders.sales-agreements.findeks-verification')->with([
                'order' => $order,
                'salesAgreement' => $order->salesAgreement
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'applicationRejected', ['e' => $e, 'order_no' => $orderNo]);

            return redirect()->route('panel')->with('error', 'Error occurred. Contact the support.');
        }
    }

    public function checkFindeksPin(Request $request, $orderNo)
    {
        try {
            $order = auth()->user()->getOrderForUser($orderNo, ['salesAgreement']);

            if (!$order || $order->isCancelled() || !$order->isSalesAgreementOrder() || !$order->erp_order_id) {
                return redirect()->route('home');
            }

            if ($order->salesAgreement && $order->salesAgreement->isDeclined()) {
                return redirect()->route('home');
            }

            if ($order->salesAgreement->stage !== SalesAgreement::STAGES['sms_pin_pending']) {
                return redirect()->route('home');
            }

            $pin = $request->input('verification_code');

            if (empty($pin)) {
                return back()->with('error', __('web.verification-code-required'));
            }

            $order->salesAgreement->update([
                'stage' => SalesAgreement::STAGES['verifying_pin']
            ]);

            dispatch(new CheckFindeksPinJob($order->salesAgreement->id, $pin))->delay(now()->addSeconds(1));

            return redirect()->route('sales-agreements.processing', ['orderNo' => $order->order_no]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'checkFindeksPin', ['e' => $e, 'order_no' => $orderNo]);

            return redirect()->route('panel')->with('error', 'Error occurred. Contact the support.');
        }
    }

    public function collectDownPayment(Request $request, $orderNo)
    {
        try {
            $order = auth()->user()->getOrderForUser($orderNo, ['salesAgreement']);

            if (!$order || $order->isCancelled() || !$order->isSalesAgreementOrder() || !$order->erp_order_id) {
                return redirect()->route('home');
            }

            if ($order->salesAgreement && $order->salesAgreement->isDeclined()) {
                return redirect()->route('home');
            }

            if (!in_array($order->salesAgreement->stage, [SalesAgreement::STAGES['collect_down_payment'], SalesAgreement::STAGES['finished']])) {
                return redirect()->route('home');
            }

            $paidState = $order->getOrderPaymentsState();

            if ($paidState['is_paid']) {
                if ($order->salesAgreement->stage !== SalesAgreement::STAGES['finished']) {
                    $order->salesAgreement->update([
                        'stage' => 'finished'
                    ]);
                }

                return redirect()->route('sales-agreements.thank-you', ['orderNo' => $orderNo]);
            }

            $bankAccounts = BankAccount::with('bank')->whereHas('bank')->get();

            return view('home.orders.sales-agreements.collect-down-payment')->with([
                'order' => $order,
                'salesAgreement' => $order->salesAgreement,
                'bankAccounts' => $bankAccounts,
                'paidState' => $paidState,
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'collectDownPayment', ['e' => $e, 'order_no' => $orderNo]);

            return redirect()->route('panel')->with('error', 'Error occurred. Contact the support.');
        }
    }

    public function thankYouPage(Request $request, $orderNo)
    {
        try {
            $order = auth()->user()->getOrderForUser($orderNo, ['salesAgreement']);

            if (!$order || $order->isCancelled() || !$order->isSalesAgreementOrder() || !$order->erp_order_id) {
                return redirect()->route('home');
            }

            if ($order->salesAgreement->stage !== SalesAgreement::STAGES['finished']) {
                $handler = new SalesAgreementHandler($order->salesAgreement);

                return $handler->navigateUserToStage();
            }

            $paidState = $order->getOrderPaymentsState();

            if (!$paidState['is_paid']) {
                return redirect()->route('sales-agreements.collect-down-payment', ['orderNo' => $orderNo]);
            }

            if (empty($order->salesAgreement->agreement_document_link)) {
                dispatch(new SalesAgreementDocumentJob($order->salesAgreement->id))->delay(now()->addSeconds(1));
            }

            return view('home.orders.sales-agreements.thank-you')->with([
                'order' => $order,
                'salesAgreement' => $order->salesAgreement,
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'thankYouPage', ['e' => $e, 'order_no' => $orderNo]);

            return redirect()->route('panel')->with('error', 'Error occurred. Contact the support.');
        }
    }

    public function proccessingPage(Request $request, $orderNo)
    {

        try {
            $order = auth()->user()->getOrderForUser($orderNo, ['salesAgreement']);

            if (!$order || $order->isCancelled() || !$order->isSalesAgreementOrder() || !$order->erp_order_id) {
                return redirect()->route('home');
            }

            return view('home.orders.sales-agreements.processing')->with([
                'order' => $order,
                'salesAgreement' => $order->salesAgreement,
                'stage' =>   __('web.' . $order->salesAgreement->stage)
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'processingPage', ['e' => $e, 'order_no' => $orderNo]);

            return redirect()->route('panel')->with('error', 'Error occurred. Contact the support.');
        }
    }

    public function checkProcessingStatus(Request $request)
    {
        try {
            $orderNo = $request->input('orderNo');

            if (empty($orderNo)) {
                return response()->json(['status' => false, 'terminated' => true]);
            }

            $order = auth()->user()->getOrderForUser($orderNo, ['salesAgreement']);

            if (!$order->salesAgreement) {
                return response()->json(['stage' => false, 'terminated' => true]);
            }

            $handler = new SalesAgreementHandler($order->salesAgreement);

            $redirectTo = $handler->getRouteForStage($handler->getCurrentStage());

            $stageTranslated = __('web.' . $order->salesAgreement->stage);

            return response()->json(['stage' => $stageTranslated, 'redirectTo' => $redirectTo]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'checkProcessingStatus', ['e' => $e, 'order_no' => $orderNo]);

            return response()->json(['status' => false, 'terminated' => true]);
        }
    }

    public function checkDocumentStatus(Request $request)
    {
        try {
            $salesAgreementText = __('web.sales-agreement');

            $orderNo = $request->input('orderNo');

            if (empty($orderNo)) {
                return response()->json(['documentUrl' => null, 'salesAgreementText' => $salesAgreementText]);
            }

            $order = auth()->user()->getOrderForUser($orderNo, ['salesAgreement']);

            if (!$order->salesAgreement->agreement_document_link) {
                return response()->json(['documentUrl' => null, 'salesAgreementText' => $salesAgreementText]);
            }

            return response()->json(['documentUrl' => $order->salesAgreement->agreement_document_link, 'salesAgreementText' => $salesAgreementText]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'checkDocumentStatus', ['e' => $e, 'order_no' => $orderNo]);

            return response()->json(['documentUrl' => null, 'salesAgreementText' => $salesAgreementText]);
        }
    }

    public function uploadNotaryDocument(Request $request, $orderNo, $type)
    {
        try {
            $order = auth()->user()->getOrderForUser($orderNo);

            if (!$order) {
                return redirect()->route('home');
            }

            if (!$order->salesAgreement) {
                return back()->with('error', __('app.error-occured'));
            }

            if ($type === 'notary_front') {
                $request->validate([
                    'notary_document_front' => 'required|file|mimes:pdf,jpeg,jpg,png',
                ], [
                    'notary_document_front.required' => __('validation.custom.notary-document-validation'),
                    'notary_document_front.mimes' => __('validation.custom.notary-document-validation'),
                ]);

                $notaryDocument = $request->file('notary_document_front');

                if (!$notaryDocument) {
                    return back()->with('error', __('web.file-is-required'));
                }

                $uploadedFileName = $order->salesAgreement->uploadNotaryDocument($notaryDocument, $type);

                if (!$uploadedFileName) {
                    return back()->with('error', __('app.error-occured'));
                }

                $order->salesAgreement->update([
                    'notary_document' => $uploadedFileName,
                    'notary_document_rejected' => false,
                    'notary_document_rejection_reason' => null
                ]);

                return back()->with('success', __('web.document-uploaded'));
            }

            if ($type === 'notary_back') {
                $request->validate([
                    'notary_document_back' => 'required|file|mimes:pdf,jpeg,jpg,png',
                ], [
                    'notary_document_back.required' => __('validation.custom.notary-document-validation'),
                    'notary_document_back.mimes' => __('validation.custom.notary-document-validation'),
                ]);

                $notaryDocumentBack = $request->file('notary_document_back');

                if (!$notaryDocumentBack) {
                    return back()->with('error', __('web.file-is-required'));
                }

                $uploadedFileName = $order->salesAgreement->uploadNotaryDocument($notaryDocumentBack, $type);

                if (!$uploadedFileName) {
                    return back()->with('error', __('app.error-occured'));
                }

                $order->salesAgreement->update([
                    'notary_document_back' => $uploadedFileName,
                    'notary_document_back_rejected' => false,
                    'notary_document_rejection_reason' => null
                ]);

                return back()->with('success', __('web.document-uploaded'));
            }

            if ($type === 'front_side_id') {
                $request->validate([
                    'front_side_id' => 'required|file|mimes:pdf,jpeg,jpg,png',
                ], [
                    'front_side_id.required' => __('validation.custom.notary-document-validation'),
                    'front_side_id.mimes' => __('validation.custom.notary-document-validation'),
                ]);

                $idDocument = $request->file('front_side_id');

                if (!$idDocument) {
                    return back()->with('error', __('web.file-is-required'));
                }

                $uploadedFileName = $order->salesAgreement->uploadNotaryDocument($idDocument, $type);

                if (!$uploadedFileName) {
                    return back()->with('error', __('app.error-occured'));
                }

                $order->salesAgreement->update([
                    'front_side_id' => $uploadedFileName,
                    'front_side_id_rejected' => false,
                    'notary_document_rejection_reason' => null
                ]);

                return back()->with('success', __('web.document-uploaded'));
            }

            return back()->with('error', 'Error occurred. Contact the support.');
        } catch (ValidationException $e) {
            LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'uploadNotaryDocument: ' . $type, ['e' => $e->errors(), 'order_no' => $orderNo]);
            return back()->with('error', $e->getMessage());
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'uploadNotaryDocument: ' . $type, ['e' => $e, 'order_no' => $orderNo]);
            return back()->with('error', 'Error occurred. Contact the support.');
        }
    }

    public function bondPaymentPage(Request $request, $orderNo)
    {
        try {
            $order = auth()->user()->orders()->with(['productVariation', 'salesAgreement'])->where('order_no', $orderNo)->first();

            if (!$order) {
                $order = auth()->user()->createdOrders()->with(['productVariation', 'salesAgreement'])->where('order_no', $orderNo)->first();
                if (!$order || $order->isCancelled()) {
                    return redirect()->route('home');
                }
            }

            if (!$order->isSalesAgreementOrder() || !$order->erp_order_id) {
                return redirect()->route('home');
            }

            if ($order->salesAgreement && $order->salesAgreement->isDeclined()) {
                return redirect()->route('home');
            }

            $bondNo = $request->input('bond_no');

            if (empty($bondNo)) {
                return redirect()->route('home');
            }

            $bonds = $order->getBondsPayments();

            if (!$bonds) {
                return redirect()->route('home');
            }

            $existingBond = $order->checkIfBondExists($bonds, $bondNo);

            if (!$existingBond) {
                return redirect()->route('home');
            }

            $bondPaymentState = $order->getBondsPaymentStates($bonds, $bondNo);

            if ($bondPaymentState['is_paid']) {
                return redirect()->route('customer.order-details', ['orderNo' => $order->order_no]);
            }

            return view('home.orders.sales-agreements.bond-payment')->with([
                'order' => $order,
                'salesAgreement' => $order->salesAgreement,
                'bondPaymentState' => $bondPaymentState,
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'bondPaymentPage', ['e' => $e, 'order_no' => $orderNo, 'bond_no' => $bondNo]);

            return redirect()->route('panel')->with('error', 'Error occurred. Contact the support.');
        }
    }

    public function processBondPayment(BondPaymentRequest $request, $orderNo)
    {
        DB::beginTransaction();

        try {
            $order = auth()->user()->orders()->where('order_no', $orderNo)->first();

            if (!$order) {
                $order = auth()->user()->createdOrders()->where('order_no', $orderNo)->first();

                if (!$order || $order->isCancelled()) {
                    DB::rollBack();
                    return back()->with('error', __('web.order-does-not-exist'));
                }
            }

            $bondNo = $request->input('bond_no');

            if (empty($bondNo)) {
                return redirect()->route('home');
            }

            $bonds = $order->getBondsPayments();

            if (!$bonds) {
                return redirect()->route('home');
            }

            $existingBond = $order->checkIfBondExists($bonds, $bondNo);

            if (!$existingBond) {
                return redirect()->route('home');
            }

            $bondPaymentState = $order->getBondsPaymentStates($bonds, $bondNo);

            $data = $request->validated();

            $paymentAmount = $data['payment_amount'];
            $customAmount = $data['custom_amount'] ?? null;
            $ccName = $data['name'] ?? null;
            $ccNumber = $data['number'] ?? null;
            $ccExpiry = $data['expiry'] ?? null;
            $ccCvc = $data['cvc'] ?? null;
            $paymentType = $data['payment_type'];

            $orderDate = $order->created_at->format('Y-m-d H:i:s');

            $remainingPaymentAmount = $bondPaymentState['remaining_amount'];

            $minPartialPaymentPercent = Configuration::getMinPartialPercent();
            $maxPaymentsCount = Configuration::getMaxPaymentsCount();
            $paymentCount = $bondPaymentState['payment_count_raw'];
            $remainingPaymentCount = $maxPaymentsCount - $paymentCount;

            if ($remainingPaymentCount < 2 && $customAmount && $customAmount < $remainingPaymentAmount) {
                DB::rollBack();
                return back()->with('error', __('web.partial-payment-limit-reached'));
            }

            $isFirstPayment = $bondPaymentState['payment_count_raw'] === 0;

            if ($isFirstPayment && $customAmount) {
                $partialPaymentPercentage = (float) $customAmount * 100 / $remainingPaymentAmount;

                if ($partialPaymentPercentage < $minPartialPaymentPercent) {
                    DB::rollBack();
                    return back()->with('error', __('web.min-partial-validation', ['min' => $minPartialPaymentPercent]));
                }
            }

            if ($paymentType == 'credit-card' && strlen($ccNumber) < 16) {
                DB::rollBack();
                return back()->with('error', __('validation.custom.number.size'));
            }

            if ($customAmount && $bondPaymentState['remaining_amount'] < $customAmount) {
                DB::rollBack();
                return back()->with('error', __('validation.partial-amount-too-big'));
            }

            if ($paymentType == 'credit-card') {
                $gateway = new CreditCardGateway();

                $expiryDate = explode('/', $ccExpiry);
                $year = strlen($expiryDate[1]) > 2 ? $expiryDate[1] : "20" . $expiryDate[1];
                $month = $expiryDate[0];

                $originalAmount = $paymentAmount == 'full' ? $remainingPaymentAmount : $customAmount;
                $amountWithRates = $originalAmount;

                if ($originalAmount > $remainingPaymentAmount) {
                    DB::rollBack();
                    return back()->with('error', __('validation.amount-too-big'));
                }

                $response = $gateway->sendBondPayment(
                    auth()->user()->id,
                    $ccName,
                    $ccNumber,
                    $ccCvc,
                    $month,
                    $year,
                    $order->order_no,
                    $orderDate,
                    $amountWithRates,
                    $order->invoice_user_id,
                    $request->ip(),
                    $bondNo
                );

                if (in_array($response['StatusCode'], [332, 601])) {
                    DB::commit();
                    return $response['Form3DContent'];
                }

                LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'processBondPayment: Payment error BT', ['response' => $response, 'order_no' => $orderNo, 'bond_no' => $bondNo]);

                DB::rollBack();
                return back()->with('error', CreditCardGateway::translateStatusToError($response['StatusCode'], app()->getLocale()));
            }

            if ($paymentType == 'bank-transfer') {
                DB::rollBack();
                return back()->with('error', 'Error occurred. Contact the support.');
            }

            return back()->with('error', __('web.unsupported-payment-type'));
        } catch (Exception $e) {
            DB::rollBack();
            LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'processBondPayment', ['e' => $e, 'order_no' => $orderNo, 'bond_no' => $bondNo]);
            return back()->with('error', 'Error occurred. Contact the support.');
        }
    }

    public function handleBondCreditCardResponse(Request $request, $orderNo)
    {
        try {
            $lang = $request->input('app_lang');

            if (!empty($lang)) {
                app()->setLocale($lang);
            }

            $jsonContent = $request->getContent();

            $response = str_replace("jsonData=", "", $jsonContent);

            $response = json_decode($response);

            $order = Order::where('order_no', $orderNo)->first();

            if (!$order) {
                return redirect()->route('home')->with('error', 'Error occurred. Contact the support.');
            }

            $isValidHash = (new CreditCardGateway)->validateIncomingHash($response, $order);

            if (!$isValidHash) {
                LoggerService::logError(LogChannelsEnum::ApplicationOrdering, 'Incorrect hash sent', ['response' => $response, 'order_id' => $order->id, 'debug' => (new CreditCardGateway)->validateIncomingHash($response, $order, true)]);

                return redirect()->route('home')->with('error', CreditCardGateway::translateStatusToError($response->StatusCode, app()->getLocale()));
            } else {
                LoggerService::logSuccess(LogChannelsEnum::ApplicationOrdering, 'Hash validated successfully', ['response' => $response, 'order_id' => $order->id]);
            }

            $bondNo = $request->input('bond_no');

            if (empty($bondNo)) {
                LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'handleBondCreditCardResponse: No bond_no was returned from payment provider', ['response' => $response, 'order_no' => $orderNo, 'bond_no' => $bondNo ?? null]);

                return redirect()->route('home');
            }

            if (!in_array($response->StatusCode, [332, 601])) {
                $orderRefNo = $response->OrderRefNo;

                $userId = explode("-", $orderRefNo);

                if (!auth()->user()) {
                    $user = User::where('id', $userId[1])->first();

                    if ($user) {
                        Auth::login($user);
                    }
                }

                $bank = Bank::where('vpos_bank_code', $response->VPosBankCode)->first();

                $bankAccountId = null;

                if ($bank) {
                    $bankAccount = BankAccount::where('bank_id', $bank->id)->first();
                    $bankAccountId = !($bankAccount) ? null : $bankAccount->id;
                } else {
                    LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'handleBondCreditCardResponse: Bank not found', ['vpos' => $response->VPosBankCode, 'order_no' => $orderNo, 'bond_no' => $bondNo ?? null]);
                }

                $order->orderPayments()->create([
                    'payment_amount' => floatval($response->Amount),
                    'payment_type' => 'K',
                    'bank_account_id' => $bankAccountId,
                    'approved_by_erp' => 'N',
                    'failed' => true,
                    'number_of_installments' => $response->Installment,
                    'collected_payment' => floatval($response->Amount),
                    'description' => "Bond payment failed: $bondNo",
                    'payment_ref_no' => $orderNo . '-failed-bond-payment',
                    'user_id' => $order->invoice_user_id,
                    'payment_gateway_response' => json_encode($response),
                    'e_bond_no' => $bondNo
                ]);

                return redirect()->route('sales-agreements.bond-payment-page', ['orderNo' => $order->order_no, 'bond_no' => $bondNo])->with('error', CreditCardGateway::translateStatusToError($response->StatusCode, app()->getLocale()));
            }

            $bankAccountId = null;

            $bank = Bank::where('vpos_bank_code', $response->VPosBankCode)->first();

            if ($bank) {
                $bankAccount = BankAccount::where('bank_id', $bank->id)->first();
                $bankAccountId = !($bankAccount) ? null : $bankAccount->id;
            } else {
                LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'handleBondCreditCardResponse: Bank not found', ['vpos' => $response->VPosBankCode, 'order_no' => $orderNo, 'bond_no' => $bondNo ?? null]);
            }

            $orderRefNo = $response->OrderRefNo;
            $userId = explode("-", $orderRefNo);

            if (!auth()->user()) {
                $user = User::where('id', $userId[1])->first();

                if ($user) {
                    Auth::login($user);
                }
            }

            if ($order->orderPayments()->where([['payment_ref_no', $orderNo . $response->PaymentID], ['e_bond_no', $bondNo]])->exists()) {
                return redirect()->route('sales-agreements.bond-payment-page', ['orderNo' => $order->order_no, 'bond_no' => $bondNo])->with('success', __('web.bond-payment-exists'));
            }

            $bondPayment = $order->orderPayments()->create([
                'payment_amount' => floatval($response->Amount),
                'payment_type' => 'K',
                'bank_account_id' => $bankAccountId,
                'approved_by_erp' => 'Y',
                'number_of_installments' => $response->Installment,
                'collected_payment' => floatval($response->Amount),
                'description' => "Bond payment: $bondNo",
                'payment_ref_no' => $orderNo . $response->PaymentID,
                'user_id' => $order->invoice_user_id,
                'payment_gateway_response' => json_encode($response),
                'e_bond_no' => $bondNo
            ]);

            if (!$bondPayment) {
                return redirect()->route('sales-agreements.bond-payment-page', ['orderNo' => $order->order_no, 'bond_no' => $bondNo])->with('error', 'Error occured. Contant the support.');
            }

            // $this->sendPaymentNoticeToERP($bondPayment, true, $bankName); // DO we send something to ERP?

            return redirect()->route('sales-agreements.bond-payment-page', ['orderNo' => $order->order_no, 'bond_no' => $bondNo])->with('success', __('web.bond-payment-success'));
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'handleBondCreditCardResponse', ['e' => $e, 'order_no' => $orderNo, 'bond_no' => $bondNo ?? null]);

            return redirect()->route('home')->with('error', 'Error occurred. Contact the support.');
        }
    }

    public function bondPaymentList(Request $request, $orderNo)
    {
        try {
            $order = auth()->user()->orders()->with(['productVariation', 'salesAgreement'])->where('order_no', $orderNo)->first();

            if (!$order) {
                $order = auth()->user()->createdOrders()->with(['productVariation', 'salesAgreement'])->where('order_no', $orderNo)->first();
                if (!$order || $order->isCancelled()) {
                    return redirect()->route('home');
                }
            }

            if (!$order->isSalesAgreementOrder() || !$order->erp_order_id) {
                return redirect()->route('home');
            }

            if ($order->salesAgreement && $order->salesAgreement->isDeclined()) {
                return redirect()->route('home');
            }

            $bondNo = $request->input('bond_no');

            if (empty($bondNo)) {
                return redirect()->route('home');
            }

            $bonds = $order->getBondsPayments();

            if (!$bonds) {
                return redirect()->route('home');
            }

            $bondPaymentState = $order->getBondsPaymentStates($bonds, $bondNo);

            if (!$bondPaymentState) {
                return redirect()->route('home');
            }

            return view('home.orders.sales-agreements.bond-payments-list')->with([
                'order' => $order,
                'bondPaymentState' => $bondPaymentState,
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'bondPaymentList', ['e' => $e, 'order_no' => $orderNo, 'bond_no' => $bondNo ?? null]);

            return redirect()->route('panel')->with('error', 'Error occurred. Contact the support.');
        }
    }
}
