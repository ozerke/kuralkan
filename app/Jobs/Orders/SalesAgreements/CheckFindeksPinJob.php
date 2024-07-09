<?php

namespace App\Jobs\Orders\SalesAgreements;

use App\Http\Controllers\Orders\SoapSendOrderController;
use App\Models\SalesAgreement;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckFindeksPinJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 60;

    protected $salesAgreementId;

    protected $pin;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($salesAgreementId, $pin)
    {
        $this->onQueue('salesagreements');
        $this->salesAgreementId = $salesAgreementId;
        $this->pin = $pin;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $salesAgreement = SalesAgreement::with(['order'])->findOrFail($this->salesAgreementId);

        LoggerService::logInfo(LogChannelsEnum::CheckFindeksPin, "[JOB]: Requesting for: {$this->salesAgreementId}");

        if ($salesAgreement->findeks_request_status != 3) {
            LoggerService::logError(LogChannelsEnum::CheckFindeksPin, 'Status is not 3, but requested to check PIN', [
                'sales_agreement_id' => $this->salesAgreementId,
            ]);
            return;
        }

        $service = new SoapSendOrderController();

        $data = [
            $salesAgreement->findeks_request_id,
            Carbon::parse($salesAgreement->order->invoiceUser->date_of_birth)->format('Y-m-d'),
            (string) $this->pin
        ];

        $confirmation = $service->findeksPinConfirmation(...$data);

        if ($confirmation == "0" || $confirmation == "2") {
            LoggerService::logError(LogChannelsEnum::CheckFindeksPin, 'Confirmation status is wrong', ['confirmation' => $confirmation, 'sales_agreement' => $salesAgreement]);

            $salesAgreement->update([
                'stage' => SalesAgreement::STAGES['sms_pin_pending']
            ]);

            $this->fail(new Exception('Findeks PIN confirmation is wrong'));
        } else {
            LoggerService::logSuccess(LogChannelsEnum::CheckFindeksPin, 'Confirmation accepted', ['confirmation' => $confirmation, 'sales_agreement' => $salesAgreement]);

            $salesAgreement->update([
                'stage' => SalesAgreement::STAGES['findeks_request_status'],
                'findeks_request_status' => null
            ]);

            dispatch(new FindeksRequestStatusJob($this->salesAgreementId))->delay(now()->addSeconds(1));
        }
    }

    public function uniqueId()
    {
        return $this->salesAgreementId;
    }
}
