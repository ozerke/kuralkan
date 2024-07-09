<?php

namespace App\Observers;

use App\Jobs\SendSMSJob;
use App\Models\OrderPayment;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use App\Services\SMSTemplateParser;
use Exception;

class OrderPaymentObserver
{
    /**
     * Handle the OrderPayment "created" event.
     */
    public function created(OrderPayment $orderPayment): void
    {
        try {
            if ($orderPayment->failed || $orderPayment->is_fee_payment) {
                return;
            }

            $order = $orderPayment->order()->with(['invoiceUser', 'productVariation'])->first();
            $fullname = $order->invoiceUser->full_name;
/*
            if ($orderPayment->payment_type === 'K') {
                // Card payment
                $isFullyPaid = $order->getOrderPaymentsState()['is_paid'];

                if ($isFullyPaid) {
                    $message = SMSTemplateParser::cardFullPayment(
                        $fullname,
                        $order->order_no,
                        $order->productVariation->getDocumentTitle(),
                        $orderPayment->payment_amount
                    );

                    dispatch(new SendSMSJob($order->invoiceUser->phone, $message));
                } else {
                    $message = SMSTemplateParser::cardPartial(
                        $fullname,
                        $order->order_no,
                        $order->productVariation->getDocumentTitle(),
                        $orderPayment->payment_amount
                    );

                    dispatch(new SendSMSJob($order->invoiceUser->phone, $message));
                }
            }
*/
            if ($orderPayment->payment_type === 'H') {

                $bankAccount = $orderPayment->bankAccount;
                $bankName = $bankAccount->bank ? $bankAccount->bank->bank_name : '';

                if ($orderPayment->approved_by_erp == 'Y') {
                    // Bank transaction information received by web service from the ERP
                    /*
                    $paymentReceivedMessage = SMSTemplateParser::bankPaymentReceived(
                        $fullname,
                        $order->order_no,
                        $order->productVariation->getDocumentTitle(),
                        $bankName,
                        $orderPayment->payment_amount
                    );
                    dispatch(new SendSMSJob($order->invoiceUser->phone, $paymentReceivedMessage));
                    */
                } else {
                    // Bank payment declared by customer
                    $firstMessage = SMSTemplateParser::bankPayment(
                        $fullname,
                        $order->order_no,
                        $order->productVariation->getDocumentTitle(),
                        $bankName,
                        $orderPayment->payment_amount
                    );

                    $secondMessage = SMSTemplateParser::bankPaymentDetails(
                        $order->order_no,
                        $bankName,
                        $bankAccount->branch_name,
                        $bankAccount->branch_code,
                        $bankAccount->account_no,
                        $bankAccount->iban
                    );

                    dispatch(new SendSMSJob($order->invoiceUser->phone, $firstMessage));
                    dispatch(new SendSMSJob($order->invoiceUser->phone, $secondMessage));
                }
            }

            if ($orderPayment->e_bond_no) {
                $orderPayment->ebond->updateRemainingAmount();
            }
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::MessagesSms, 'OrderPaymentObserver', ['e' => $e]);
        }
    }
}
