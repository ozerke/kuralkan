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

class FindeksMergeOrderJob implements ShouldQueue, ShouldBeUnique
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
        $salesAgreement = SalesAgreement::with(['order'])->findOrFail($this->salesAgreementId);

        LoggerService::logInfo(LogChannelsEnum::FindeksMergeOrder, "[JOB]: Requesting for: {$this->salesAgreementId}");

        if (!$salesAgreement->findeks_request_result || $salesAgreement->findeks_merged_order) {
            LoggerService::logError(LogChannelsEnum::FindeksMergeOrder, 'Duplicate job for passed stage or findeks_request_result is null', [
                'sales_agreement' => $salesAgreement,
            ]);
            return;
        }

        $salesAgreement->update([
            'approval_status' => 'approved'
        ]);

        $data = [
            $salesAgreement->order->invoiceUser->national_id,
            $salesAgreement->order->salesAgreement->findeks_request_id,
            $salesAgreement->order->erp_prefix,
            $salesAgreement->order->erp_order_id
        ];

        $service = new SoapSendOrderController();
        $mergeRequest = $service->findeksMergeOrder(...$data);

        if ($mergeRequest == '1') {
            $salesAgreement->update([
                'findeks_merged_order' => $mergeRequest,
                'stage' => SalesAgreement::STAGES['collect_down_payment']
            ]);

            LoggerService::logSuccess(LogChannelsEnum::FindeksMergeOrder, 'Merge response success', [
                'sales_agreement_id' => $this->salesAgreementId,
                'response' => $mergeRequest
            ]);
        } else {
            $salesAgreement->update([
                'stage' => SalesAgreement::STAGES['retry_later'],
                'retry_count' => config('app.max_sa_retries', 5)
            ]);

            LoggerService::logError(LogChannelsEnum::FindeksMergeOrder, 'Merge response not success, redirecting to retry_later stage', [
                'sales_agreement' => $salesAgreement,
                'response' => $mergeRequest
            ]);

            $this->fail(new Exception('Merge response not success'));
        }
    }

    public function uniqueId()
    {
        return $this->salesAgreementId;
    }
}
