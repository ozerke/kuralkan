<?php

namespace App\Jobs\Orders\SalesAgreements;

use App\Http\Controllers\Orders\SoapSendOrderController;
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

class FindeksRequestStatusJob implements ShouldQueue, ShouldBeUnique
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
        $this->tries = config('app.max_sa_retries', 5);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $salesAgreement = SalesAgreement::findOrFail($this->salesAgreementId);

        if ($salesAgreement->findeks_request_status == 5 || !is_null($salesAgreement->findeks_request_result)) {
            LoggerService::logError(LogChannelsEnum::FindeksRequestStatus, 'Duplicate job for passed stage', [
                'sales_agreement_id' => $this->salesAgreementId,
            ]);
            return;
        }

        $service = new SoapSendOrderController();
        $status = $service->findeksRequestStatus($salesAgreement->findeks_request_id);

        switch ($status) {
            case 3:
                $salesAgreement->update([
                    'is_sms_pending' => true,
                    'findeks_request_status' => $status,
                    'stage' => SalesAgreement::STAGES['sms_pin_pending']
                ]);

                LoggerService::logInfo(LogChannelsEnum::FindeksRequestStatus, 'Status 3 received, displaying input screen', [
                    'sales_agreement_id' => $this->salesAgreementId,
                    'status' => $status
                ]);

                break;

            case 5:
                $salesAgreement->update([
                    'is_sms_pending' => false,
                    'findeks_request_status' => $status,
                    'stage' => SalesAgreement::STAGES['findeks_request_result']
                ]);

                LoggerService::logSuccess(LogChannelsEnum::FindeksRequestStatus, 'Status 5 received, moving to next stage', [
                    'sales_agreement_id' => $this->salesAgreementId,
                    'status' => $status
                ]);

                dispatch(new FindeksRequestResultJob($this->salesAgreementId))->delay(now()->addSeconds(1));
                break;

            default:
                $salesAgreement->update([
                    'retry_count' => $salesAgreement->retry_count + 1,
                    'findeks_request_status' => $status,
                    'stage' => SalesAgreement::STAGES['findeks_request_status']
                ]);

                LoggerService::logError(LogChannelsEnum::FindeksRequestStatus, 'Retrying', [
                    'sales_agreement_id' => $this->salesAgreementId,
                    'status' => $status
                ]);

                throw new Exception('Retrying');

                break;
        }
    }

    /**
     * Handle a job failure.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        $salesAgreement = SalesAgreement::findOrFail($this->salesAgreementId);

        $salesAgreement->update([
            'is_sms_pending' => false,
            'findeks_request_status' => 0,
            'stage' => SalesAgreement::STAGES['retry_later']
        ]);

        LoggerService::logError(LogChannelsEnum::FindeksRequestStatus, 'Maximum retries reached', [
            'sales_agreement_id' => $this->salesAgreementId
        ]);
    }

    public function uniqueId()
    {
        return $this->salesAgreementId;
    }
}
