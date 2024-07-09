<?php

namespace App\Observers;

use App\Jobs\SendEmailJob;
use App\Jobs\SendSMSJob;
use App\Services\SMSTemplateParser;
use App\Mail\OrderCreatedMail;
use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        $order->update([
            'ordering_user_fullname' => $order->user->fullname,
            'invoice_user_fullname' => $order->invoiceUser->fullname,
            'delivery_user_fullname' => $order->deliveryUser->fullname,
        ]);

        if($order->payment_type != 'S') 
        {
            $message = SMSTemplateParser::orderProcessed(
                $order->invoiceUser->full_name,
                $order->order_no
            );

            dispatch(new SendSMSJob($order->invoiceUser->phone, $message));
            dispatch(new SendEmailJob($order->invoiceUser->email, new OrderCreatedMail($order)));
        }
    }
}
