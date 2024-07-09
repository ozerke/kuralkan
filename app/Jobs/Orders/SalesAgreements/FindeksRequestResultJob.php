<?php

namespace App\Jobs\Orders\SalesAgreements;

use App\Http\Controllers\Orders\SoapSendOrderController;
use App\Models\SalesAgreement;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FindeksRequestResultJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 60;

    protected $salesAgreementId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($salesAgreementId)
    {
        $this->onQueue('salesagreements');
        $this->salesAgreementId = $salesAgreementId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $salesAgreement = SalesAgreement::findOrFail($this->salesAgreementId);

        LoggerService::logInfo(LogChannelsEnum::FindeksRequestResult, "[JOB]: Requesting for: {$this->salesAgreementId}");

        if ($salesAgreement->findeks_request_status != 5 || !is_null($salesAgreement->findeks_request_result)) {
            LoggerService::logError(LogChannelsEnum::FindeksRequestResult, 'Duplicate job for passed stage', [
                'sales_agreement' => $salesAgreement,
            ]);
            return;
        }

        $service = new SoapSendOrderController();
        $requestResult = $service->findeksRequestResult($salesAgreement->findeks_request_id);

        if ($requestResult == '1') {
            $salesAgreement->update([
                'stage' => SalesAgreement::STAGES['findeks_merge_order'],
                'findeks_request_result' => $requestResult
            ]);

            LoggerService::logSuccess(LogChannelsEnum::FindeksRequestResult, 'Application approved', ['sales_agreement' => $salesAgreement, 'requestResult' => $requestResult]);

            dispatch(new FindeksMergeOrderJob($this->salesAgreementId))->delay(now()->addSeconds(1));

            return;
        } else {
            LoggerService::logError(LogChannelsEnum::FindeksRequestResult, 'Application rejected', ['sales_agreement' => $salesAgreement, 'requestResult' => $requestResult]);

            $salesAgreement->update([
                'approval_status' => 'declined',
                'findeks_request_result' => $requestResult ?? 0,
                'stage' => SalesAgreement::STAGES['declined'],
            ]);

            $salesAgreement->order->updateOrderStatus('cancelled');

            return;
        }
    }

    public function uniqueId()
    {
        return $this->salesAgreementId;
    }
}
