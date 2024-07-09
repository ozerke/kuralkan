<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Orders\SoapSendOrderController;
use App\Http\Requests\Payments\PaymentRequest;
use App\Jobs\SendErpPaymentJob;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Campaign;
use App\Models\Configuration;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\User;
use App\Services\CreditCardGateway;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderPaymentsController extends Controller
{
    public function orderPayment(Request $request, $orderNo)
    {
        try {
            $order = auth()->user()->orders()->with('productVariation')->where('order_no', $orderNo)->first();

            if (!$order) {
                $order = auth()->user()->createdOrders()->with('productVariation')->where('order_no', $orderNo)->first();
            }

            if (!$order || $order->isCancelled()) {
                return redirect()->route('home');
            }

            $paidState = $order->getOrderPaymentsState();

            if ($order->isSalesAgreementOrder()) {
                return redirect()->route('sales-agreements.payment-plan', ['orderNo' => $orderNo]);
            }

            if ($paidState['is_paid']) {
                return redirect()->route('thank-you', ['orderNo' => $orderNo]);
            }

            $bankAccounts = BankAccount::with('bank')->whereHas('bank')->get();

            return view('home.orders.payment')->with([
                'order' => $order,
                'bankAccounts' => $bankAccounts,
                'paidState' => $paidState,
                'campaigns' => $order->getCampaignsForOrder()
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ApplicationOrdering, 'orderPayment', ['e' => $e]);
            return redirect()->route('home')->with('error', 'Error occurred. Contact the support.');
        }
    }

    public function processPayment(PaymentRequest $request, $orderNo)
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

            $data = $request->validated();

            $paymentAmount = $data['payment_amount'];
            $customAmount = $data['custom_amount'] ?? null;
            $ccName = $data['name'] ?? null;
            $ccNumber = $data['number'] ?? null;
            $ccExpiry = $data['expiry'] ?? null;
            $ccCvc = $data['cvc'] ?? null;
            $paymentType = $data['payment_type'];
            $selectedBankId = $data['selected_bank'] ?? null;
            $numberOfInstallments = $data['number_of_installments'] ?? null;

            $campaignId = $data['campaign'] ?? null;

            if ($campaignId) {
                $campaign = Campaign::findOrFail($campaignId);

                $order->orderCampaign()->create([
                    'down_payment' => $campaign->getDownPaymentAmount($order, false),
                    'installments' => $campaign->installments,
                    'bt_payment_exp_code' => $campaign->bt_payment_exp_code,
                    'rate' => $campaign->rate,
                    'is_down_payment_bank' => $paymentType == 'bank-transfer'
                ]);
            }

            $orderDate = $order->created_at->format('Y-m-d H:i:s');

            $paymentState = $order->getOrderPaymentsState();
            $isCampaignOrder = $order->isCampaignOrder();

            $remainingPaymentAmount = $isCampaignOrder ? $paymentState['campaign_remaining_amount'] : $paymentState['remaining_amount'];

            $minPartialPaymentPercent = Configuration::getMinPartialPercent();
            $maxPaymentsCount = Configuration::getMaxPaymentsCount();
            $paymentCount = $order->orderPayments()->failedStatus(false)->count();
            $remainingPaymentCount = $maxPaymentsCount - $paymentCount;

            if (!$isCampaignOrder && $remainingPaymentCount < 2 && $customAmount && $customAmount < $remainingPaymentAmount) {
                DB::rollBack();
                return back()->with('error', __('web.partial-payment-limit-reached'));
            }

            $isFirstPayment = $order->orderPayments()->count() === 0;

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

            if (!$isCampaignOrder && $customAmount && $paymentState['remaining_amount'] < $customAmount) {
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

                if (!$isCampaignOrder && $originalAmount > $remainingPaymentAmount) {
                    DB::rollBack();
                    return back()->with('error', __('validation.amount-too-big'));
                }

                if ($numberOfInstallments > 1) {
                    $amountWithRates = Bank::calculateTotalPriceWithRates(
                        $originalAmount,
                        $numberOfInstallments,
                        $ccNumber,
                        $isCampaignOrder ? $order->orderCampaign->bt_payment_exp_code : null
                    );
                }
                /*
                // 20240128 - OE - commented as ignored by the code
                if (config('app.test_payments')) {
                    $amountWithRates = $amountWithRates / 100000;
                }
                // 20240128 - OE - commented as ignored by the code
                */
                $response = $gateway->sendPayment(
                    auth()->user()->id,
                    $ccName,
                    $ccNumber,
                    $ccCvc,
                    $month,
                    $year,
                    $numberOfInstallments ?? 1,
                    $order->order_no,
                    $orderDate,
                    $amountWithRates,
                    $originalAmount,
                    $order->invoice_user_id,
                    $request->ip(),
                    $isCampaignOrder ? $order->orderCampaign->bt_payment_exp_code : null
                );

                if (in_array($response['StatusCode'], [332, 601])) {
                    DB::commit();
                    return $response['Form3DContent'];
                }

                LoggerService::logError(LogChannelsEnum::ApplicationOrdering, 'processPayment: Payment error BT', ['response' => $response, 'order_no' => $order->order_no]);

                DB::rollBack();
                return back()->with('error', CreditCardGateway::translateStatusToError($response['StatusCode'], app()->getLocale()));
            }

            if ($paymentType == 'bank-transfer') {
                $originalAmount = $paymentAmount == 'full' ? $remainingPaymentAmount : $customAmount;

                if (!$isCampaignOrder && $originalAmount > $remainingPaymentAmount) {
                    DB::rollBack();
                    return back()->with('error', __('validation.amount-too-big'));
                }

                try {
                    $this->handleBankPayment($order, $originalAmount, $selectedBankId);
                } catch (Exception $e) {
                    DB::rollBack();
                    return back()->with('error', $e->getMessage());
                }

                DB::commit();
                return back()->with('success', __('web.partial-amount-payment-created'));
            }

            return back()->with('error', __('web.unsupported-payment-type'));
        } catch (Exception $e) {
            DB::rollBack();
            LoggerService::logError(LogChannelsEnum::ApplicationOrdering, 'processPayment', ['e' => $e]);
            return back()->with('error', 'Error occurred. Contact the support.');
        }
    }

    public function handleCreditCardResponse(Request $request, $orderNo)
    {
        try {
            $lang = $request->input('app_lang');

            if (!empty($lang)) {
                App::setLocale($lang);
            }

            $jsonContent = $request->getContent();

            $response = str_replace("jsonData=", "", $jsonContent);

            $response = json_decode($response);

            $order = Order::where('order_no', $orderNo)->first();

            $isSalesAgreement = $order->isSalesAgreementOrder();

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
                    LoggerService::logError(LogChannelsEnum::ApplicationOrdering, 'handleCreditCardResponse: Bank not found', ['vpos' => $response->VPosBankCode, 'order_no' => $order->order_no]);
                }

                $message = [
                    'StatusCode' => $response->StatusCode,
                    'StatusMessage' => $response->StatusMessage
                ];

                $orderPayment = $order->orderPayments()->create([
                    'payment_amount' => floatval($response->Amount),
                    'payment_type' => 'K',
                    'bank_account_id' => $bankAccountId,
                    'approved_by_erp' => 'N',
                    'failed' => true,
                    'number_of_installments' => $response->Installment,
                    'collected_payment' => floatval($response->Amount),
                    'description' => print_r($message, true),
                    'payment_ref_no' => $orderNo . '-failed',
                    'user_id' => $order->invoice_user_id,
                    'payment_gateway_response' => json_encode($response)
                ]);

                return redirect()->route('order-payment', ['orderNo' => $order->order_no])->with('error', CreditCardGateway::translateStatusToError($response->StatusCode, app()->getLocale()));
            }

            // 20240128 - OE $bankName = Binlist::findBankNameByDigits($ccNumberDigits);
            // 20240128 - OE - Added vpos_bank_code into the banks table so we can lookup the bank of VPOS
            $bankAccountId = null;

            $bank = Bank::where('vpos_bank_code', $response->VPosBankCode)->first();

            if ($bank) {
                $bankAccount = BankAccount::where('bank_id', $bank->id)->first();
                $bankAccountId = !($bankAccount) ? null : $bankAccount->id;
            } else {
                LoggerService::logError(LogChannelsEnum::ApplicationOrdering, 'handleCreditCardResponse: Bank not found', ['vpos' => $response->VPosBankCode, 'order_no' => $order->order_no]);
            }
            // 20240128 - OE - Added vpos_bank_code into the banks table so we can lookup the bank of VPOS

            $orderRefNo = $response->OrderRefNo;
            $userId = explode("-", $orderRefNo);

            $paymentAmount = $response->ReservedField;

            if (!auth()->user()) {
                $user = User::where('id', $userId[1])->first();

                if ($user) {
                    Auth::login($user);
                }
            }
            // 20240126 - OE - Added $orderNo like bank payments into the payment_ref_no per requirement by the IT
            // 20240128 - OE - Added bank_account_id related to the VPosBakCode sent as response
            // 20240128 - OE - IT requested the approved CC payments to be recorded as approved

            if ($order->orderPayments()->where('payment_ref_no', $orderNo . $response->PaymentID)->exists()) {
                // if ($isSalesAgreement) {
                //     return redirect()->route('sales-agreements.collect-down-payment', ['orderNo' => $order->order_no])->with('success', __('web.partial-amount-payment-created'));
                // }

                return redirect()->route('order-payment', ['orderNo' => $order->order_no])->with('success', __('web.partial-amount-payment-created'));
            }

            $orderPayment = $order->orderPayments()->create([
                'payment_amount' => floatval($paymentAmount),
                'payment_type' => 'K',
                'bank_account_id' => $bankAccountId,
                'approved_by_erp' => 'Y',
                'number_of_installments' => $response->Installment,
                'collected_payment' => floatval($response->Amount),
                'description' => $isSalesAgreement ? 'Down payment' : 'Online payment from website (creditcard).',
                'payment_ref_no' => $orderNo . $response->PaymentID,
                'user_id' => $order->invoice_user_id,
                'payment_gateway_response' => json_encode($response)
            ]);

            if (!$orderPayment) {
                // if ($isSalesAgreement) {
                //     return redirect()->route('sales-agreements.collect-down-payment', ['orderNo' => $order->order_no])->with('error', 'Error occured. Contant the support.');
                // }

                return redirect()->route('order-payment', ['orderNo' => $order->order_no])->with('error', 'Error occured. Contant the support.');
            }

            if (!$isSalesAgreement) {
                $order->update([
                    'payment_type' => 'K'
                ]);
            }

            // $this->sendPaymentNoticeToERP($orderPayment, true, $bankName);

            // if ($isSalesAgreement) {
            //     return redirect()->route('sales-agreements.collect-down-payment', ['orderNo' => $order->order_no])->with('success', __('web.partial-amount-payment-created'));
            // }

            return redirect()->route('order-payment', ['orderNo' => $order->order_no])->with('success', __('web.partial-amount-payment-created'));
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ApplicationOrdering, 'handleCreditCardResponse', ['e' => $e]);

            return false;
        }
    }

    public function handleBankPayment(Order $order, $paymentAmount, $bankAccountId)
    {
        $paymentRefNo = OrderPayment::generatePaymentRefNo($order->order_no);

        $isSalesAgreement = $order->isSalesAgreementOrder();

        $orderPayment = $order->orderPayments()->create([
            'payment_amount' => $paymentAmount,
            'payment_type' => 'H',
            'bank_account_id' => $bankAccountId,
            'number_of_installments' => 1,
            'collected_payment' => $paymentAmount,
            'description' => $isSalesAgreement ? 'Down payment' : 'Online payment from website',
            'payment_ref_no' => $paymentRefNo,
            'user_id' => $order->invoice_user_id
        ]);

        if (!$isSalesAgreement) {
            $order->update([
                'payment_type' => 'H'
            ]);
        }

        return $orderPayment;
    }

    public function orderPaidIndex(Request $request, $orderNo)
    {
        try {
            $order = auth()->user()->orders()->with('productVariation')->where('order_no', $orderNo)->first();

            if (!$order) {
                $order = auth()->user()->createdOrders()->with('productVariation')->where('order_no', $orderNo)->first();
                if (!$order) {
                    return redirect()->route('home');
                }
            }

            $paidState = $order->getOrderPaymentsState();

            if ($paidState['is_paid']) {
                return view('home.orders.thank-you')->with([
                    'order' => $order,
                ]);
            }

            return redirect()->route('home')->with('error', 'Error occurred. Contact the support.');
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ApplicationOrdering, 'orderPaidIndex', ['e' => $e]);
            return redirect()->route('home')->with('error', 'Error occurred. Contact the support.');
        }
    }

    public function cancelPayment(Request $request, $paymentRefNo)
    {
        try {
            $payment = auth()->user()->orderPayments()->approved(false)->failedStatus(false)->where('payment_ref_no', $paymentRefNo)->first();

            if (!$payment) {
                return back()->with('error', __('app.payment-not-found'));
            }

            $payment->delete();

            return back()->with('success', __('app.payment-cancelled'));
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ApplicationOrdering, 'cancelPayment', ['e' => $e]);
            return redirect()->route('home')->with('error',  __('app.error-occured'));
        }
    }

    public function redirectToPaymentPage(Request $request, $orderNo)
    {
        try {
            $order = auth()->user()->orders()->where('order_no', $orderNo)->first();

            if (!$order) {
                $order = auth()->user()->createdOrders()->where('order_no', $orderNo)->first();
            }

            if (!$order) {
                return redirect()->route('home')->with('error',  __('app.error-occured'));
            }

            $payments = $order->orderPayments()->approved(false)->failedStatus(false);
            $payments->delete();

            return redirect()->route('order-payment', ['orderNo' => $orderNo]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ApplicationOrdering, 'redirectToPaymentPage', ['e' => $e]);
            return redirect()->route('home')->with('error',  __('app.error-occured'));
        }
    }
}
