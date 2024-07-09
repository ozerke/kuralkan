<?php

namespace App\Jobs;

use App\Http\Controllers\Orders\SoapSendOrderController;
use App\Models\OrderPayment;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendErpPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    protected $orderPayment;
    protected $isCcPayment;
    protected $ccBankName;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(OrderPayment $orderPayment, $isCcPayment, $ccBankName = '')
    {
        $this->orderPayment = $orderPayment;
        $this->isCcPayment = $isCcPayment;
        $this->ccBankName = $ccBankName;
    }

    public function backoff(): array
    {
        return [5, 10, 30];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $orderBankName = $this->orderPayment->bank_account_id ? $this->orderPayment->bankAccount->bank->erp_bank_name : '';

        $bankName = $this->isCcPayment ? $this->ccBankName : $orderBankName;

        $service = new SoapSendOrderController();

        $response = $service->sendPayment(
            $this->orderPayment->order->erp_order_id,
            $this->orderPayment->payment_ref_no,
            $this->orderPayment->user->erp_user_id,
            $this->orderPayment->collected_payment,
            $this->orderPayment->payment_type,
            $bankName,
            $this->orderPayment->number_of_installments,
            $this->orderPayment->order->erp_prefix
        );

        if (!$response['response']) {
            LoggerService::logError(LogChannelsEnum::ErpDirect, 'Send ERP Payment Job', ['orderPayment' => $this->orderPayment]);

            throw new Exception("Failed ERP payment send");
        }
    }
}
