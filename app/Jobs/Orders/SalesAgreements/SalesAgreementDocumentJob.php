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

class SalesAgreementDocumentJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
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

        if (!empty($salesAgreement->agreement_document_link)) {
            LoggerService::logError(LogChannelsEnum::SalesAgreementDocument, 'Document already exists', [
                'sales_agreement_id' => $this->salesAgreementId,
            ]);

            return;
        }

        $service = new SoapSendOrderController();

        $url = $service->salesAgreementDocs($salesAgreement->findeks_request_id, 1);

        if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
            LoggerService::logError(LogChannelsEnum::SalesAgreementDocument, 'Bad document URL', ['url' => $url, 'sales_agreement' => $salesAgreement]);

            $this->fail(new Exception('Bad document URL'));
        } else {
            LoggerService::logSuccess(LogChannelsEnum::SalesAgreementDocument, 'SA document URL obtained', ['url' => $url, 'sales_agreement' => $salesAgreement]);

            $salesAgreement->update([
                'agreement_document_link' => $url,
                'stage' => SalesAgreement::STAGES['finished']
            ]);
        }
    }

    public function uniqueId()
    {
        return $this->salesAgreementId;
    }
}
