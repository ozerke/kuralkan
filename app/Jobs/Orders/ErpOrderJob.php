<?php

namespace App\Jobs\Orders;

use App\Http\Controllers\Orders\SoapSendOrderController;
use App\Jobs\SendEmailJob;
use App\Jobs\SendSMSJob;
use App\Mail\OrderCreatedMail;
use App\Models\Order;
use App\Models\User;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use App\Services\SMSTemplateParser;
use App\Utils\PDFDocuments;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ErpOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $timeout = 1800;

    protected $order;
    protected $customer;
    protected $deliveryPoint;
    protected $salesPointUser;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order, User $customer, User $deliveryPoint, ?User $salesPointUser = null)
    {
        $this->onQueue('orders');

        $this->order = $order;
        $this->customer = $customer;
        $this->deliveryPoint = $deliveryPoint;
        $this->salesPointUser = $salesPointUser;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $service = new SoapSendOrderController();

        if ($this->salesPointUser) {
            // shop order
            $erpOrder = $service->submitOrder($this->order->productVariation, $this->customer, $this->deliveryPoint, $this->order, $this->salesPointUser);

            if (!$erpOrder || !$erpOrder['erpOrderId'] || !$erpOrder['erpUserId']) {
                LoggerService::logError(LogChannelsEnum::ErpOrder, "ErpOrderId or ErpUserId is missing from the response", ['order_no' => $this->order->order_no]);

                $this->order->updateOrderStatus('cancelled');

                $this->order->update([
                    'erp_request_error' => true
                ]);
            } else {
                $this->handleOrderInformation($erpOrder);
            }
        } else {
            // customer order
            $erpOrder = $service->submitOrder($this->order->productVariation, $this->customer, $this->deliveryPoint, $this->order);

            if (!$erpOrder || !$erpOrder['erpOrderId'] || !$erpOrder['erpUserId']) {
                LoggerService::logError(LogChannelsEnum::ErpOrder, "ErpOrderId or ErpUserId is missing from the response", ['order_no' => $this->order->order_no]);

                $this->order->updateOrderStatus('cancelled');

                $this->order->update([
                    'erp_request_error' => true
                ]);
            } else {
                $this->handleOrderInformation($erpOrder);
            }
        }
    }

    private function handleOrderInformation($erpOrder)
    {
        $this->customer->update([
            'erp_user_id' => $erpOrder['erpUserId']
        ]);

        $erpAmount = round(floatval($erpOrder['amount']));

        $priceDifference = $this->order->total_amount - $erpAmount;

        $priceDifference = $priceDifference < 0 ? $priceDifference * -1 : $priceDifference;

        if ($priceDifference > 2) {
            throw new Exception('Prices do not match.');
        }

        $this->order->update([
            'erp_order_id' => $erpOrder['erpOrderId'],
            'erp_prefix' => $erpOrder['erpPrefix'],
            'erp_response_at' => Carbon::now()
        ]);

        $this->order->updateOrderStatus('order-received');

        PDFDocuments::generateContract($this->order);

        $message = SMSTemplateParser::orderProcessed(
            $this->customer->full_name,
            $this->order->order_no
        );

        dispatch(new SendSMSJob($this->customer->phone, $message));
        dispatch(new SendEmailJob($this->order->invoiceUser->email, new OrderCreatedMail($this->order)));
    }
}
