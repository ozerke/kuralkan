<?php

namespace App\Jobs;

use App\Http\Controllers\SoapServicesLibController;
use App\Models\Bank;
use App\Models\InstallmentRate;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateBankInstallmentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->onQueue('erpjobs');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $soapClient = new SoapServicesLibController();
            $installments = $soapClient->ccInstallmentList();

            LoggerService::logInfo(LogChannelsEnum::UpdateBankInstallments, 'Installments', ['installments' => $installments]);

            $collection = collect($installments)['ROW'];

            $collection = collect($collection)->map(function ($item) {
                $item = json_decode(json_encode($item), true);

                return [
                    'bank_name' => $item['BANKA'],
                    'number_of_months' => $item['TAKSIT'],
                    'rate' => $item['ORAN'],
                ];
            });

            InstallmentRate::truncate();

            foreach ($collection as $erpInstallment) {
                $bank = Bank::firstOrCreate(
                    ['erp_bank_name' => $erpInstallment['bank_name']],
                    ['bank_name' => $erpInstallment['bank_name'], 'bank_name' => $erpInstallment['bank_name']]
                );

                $installment = $bank->installmentRates()->where([
                    ['number_of_months', $erpInstallment['number_of_months']],
                ])->first();

                if (!$installment) {
                    $bank->installmentRates()->create([
                        'number_of_months' => $erpInstallment['number_of_months'],
                        'rate' => $erpInstallment['rate']
                    ]);
                } else {
                    $installment->update([
                        'number_of_months' => $erpInstallment['number_of_months'],
                        'rate' => $erpInstallment['rate']
                    ]);
                }
            }

            LoggerService::logSuccess(LogChannelsEnum::UpdateBankInstallments, 'Installments');
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::UpdateBankInstallments, 'Error in handler', ['e' => $e]);
        }
    }
}
