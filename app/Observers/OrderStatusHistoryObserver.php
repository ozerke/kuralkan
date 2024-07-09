<?php

namespace App\Observers;

use App\Jobs\SendEmailJob;
use App\Jobs\SendSMSJob;
use App\Mail\OrderConfirmedMail;

use App\Mail\OrderSupplyingMail;
use App\Mail\OrderShippedMail;
use App\Mail\OrderDeliveredMail;

use App\Models\OrderStatusHistory;
use App\Services\SMSTemplateParser;

class OrderStatusHistoryObserver
{
    /**
     * Handle the OrderStatusHistory "created" event.
     */
    public function created(OrderStatusHistory $orderStatusHistory): void
    {
        if ($orderStatusHistory->order->latest_order_status_id === $orderStatusHistory->order_status_id) {
            return;
        }

        $lastSmsStatus = $orderStatusHistory->order->sms_status;
        $incomingStatus = $orderStatusHistory->order_status_id;

        if ($lastSmsStatus >= $incomingStatus) {
            return;
        }

        $orderStatusHistory->order->update([
            'latest_order_status_id' => $orderStatusHistory->order_status_id,
            'sms_status' => $incomingStatus
        ]);

        switch ($orderStatusHistory->order_status_id) {
            case 2:

                $fullname = $orderStatusHistory->order->invoiceUser->full_name;

                $message = SMSTemplateParser::orderConfirmed(
                    $fullname,
                    $orderStatusHistory->order->order_no
                );

                dispatch(new SendSMSJob($orderStatusHistory->order->invoiceUser->phone, $message, $orderStatusHistory->order->order_no));

                dispatch(new SendEmailJob($orderStatusHistory->order->invoiceUser->email, new OrderConfirmedMail($orderStatusHistory->order)));
                break;
            case 3:
                $fullname = $orderStatusHistory->order->invoiceUser->full_name;

                $message = SMSTemplateParser::orderSupplying(
                    $fullname,
                    $orderStatusHistory->order->order_no,
                    $orderStatusHistory->order->getDeliveryDate()
                );

                dispatch(new SendSMSJob($orderStatusHistory->order->invoiceUser->phone, $message, $orderStatusHistory->order->order_no));
                dispatch(new SendEmailJob($orderStatusHistory->order->invoiceUser->email, new OrderSupplyingMail($orderStatusHistory->order)));
                break;
            case 4:
                $fullname = $orderStatusHistory->order->invoiceUser->full_name;

                $message = SMSTemplateParser::orderShipped(
                    $fullname,
                    $orderStatusHistory->order->order_no
                );

                dispatch(new SendSMSJob($orderStatusHistory->order->invoiceUser->phone, $message, $orderStatusHistory->order->order_no));
                dispatch(new SendEmailJob($orderStatusHistory->order->invoiceUser->email, new OrderShippedMail($orderStatusHistory->order)));
                break;
            case 5:
                $fullname = $orderStatusHistory->order->invoiceUser->full_name;

                $message = SMSTemplateParser::orderDelivered(
                    $fullname,
                    $orderStatusHistory->order->order_no,
                    $orderStatusHistory->order->deliveryUser->full_name
                );

                dispatch(new SendSMSJob($orderStatusHistory->order->invoiceUser->phone, $message, $orderStatusHistory->order->order_no));
                dispatch(new SendEmailJob($orderStatusHistory->order->invoiceUser->email, new OrderDeliveredMail($orderStatusHistory->order)));
                break;
            default:
                break;
        }
    }
}
