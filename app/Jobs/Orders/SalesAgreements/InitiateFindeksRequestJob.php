<?php

namespace App\Jobs\Orders\SalesAgreements;

use App\Handlers\SalesAgreementHandler;
use App\Http\Controllers\Orders\SoapSendOrderController;
use App\Models\Order;
use App\Models\SalesAgreement;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class InitiateFindeksRequestJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $timeout = 60;

    protected $order;

    protected $handler;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->onQueue('salesagreements');

        $this->order = $order;

        $this->handler = new SalesAgreementHandler($order->salesAgreement);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $service = new SoapSendOrderController();

            $erpUserId = $this->order->user->isShopOrService() ? $this->order->user->erp_user_id : '';

            $data = [
                $this->order->invoiceUser->national_id,
                $this->order->invoiceUser->full_name,
                $this->order->invoiceUser->phone,
                $this->order->productVariation->product->stock_code,
                $this->order->productVariation->color->color_code,
                $this->order->product_name,
                $this->order->total_amount,
                $this->order->salesAgreement->down_payment_amount,
                $this->order->salesAgreement->monthly_payment,
                $this->order->salesAgreement->number_of_installments,
                $this->order->invoiceUser->date_of_birth,
                $erpUserId
            ];

            $responseId = $service->initiateFindeksRequest(...$data);

            LoggerService::logInfo(LogChannelsEnum::InitiateFindeksRequest, "[JOB]: Requesting for: {$this->order->order_no}", ['response' => $responseId, 'order_no' => $this->order->order_no,  'data' => $data]);

            if (!is_numeric($responseId)) {
                LoggerService::logError(LogChannelsEnum::InitiateFindeksRequest, "[JOB]: Error for: {$this->order->order_no}", ['response' => $responseId, 'order_no' => $this->order->order_no]);

                $this->order->salesAgreement->update([
                    'approval_status' => 'declined',
                    'stage' => SalesAgreement::STAGES['declined']
                ]);

                $this->order->updateOrderStatus('cancelled');
            } else {
                $this->order->salesAgreement->update([
                    'findeks_request_id' => $responseId,
                    'stage' => SalesAgreement::STAGES['findeks_request_status']
                ]);

                dispatch(new FindeksRequestStatusJob($this->order->salesAgreement->id))->delay(now()->addSeconds(2));
            }
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::InitiateFindeksRequest, 'Error in handler', ['e' => $e, 'order_no' => $this->order->order_no]);

            $this->order->salesAgreement->update([
                'approval_status' => 'declined'
            ]);

            $this->order->updateOrderStatus('cancelled');
        }
    }

    public function uniqueId()
    {
        return $this->order->id;
    }
}
