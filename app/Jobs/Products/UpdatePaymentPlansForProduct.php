<?php

namespace App\Jobs\Products;

use App\Http\Controllers\Orders\SoapSendOrderController;
use App\Models\Product;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use App\Utils\SoapUtils;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdatePaymentPlansForProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    protected $product;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Product $product)
    {
        $this->onQueue('erpjobs');
        $this->product = $product;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $service = new SoapSendOrderController();

        $productPlans = collect();

        $this->product->downPayments()->delete();

        try {
            if ($this->product->stock_code) {
                $downPayments = $service->downPayments($this->product->stock_code);

                if (!$downPayments) {
                    LoggerService::logInfo(LogChannelsEnum::UpdatePaymentPlansForProduct, 'No $downPayments', ['stock_code' => $this->product->stock_code]);

                    return;
                }

                if (!property_exists($downPayments, 'ROW')) {
                    LoggerService::logInfo(LogChannelsEnum::UpdatePaymentPlansForProduct, 'No ROW', ['stock_code' => $this->product->stock_code]);

                    return;
                }

                $downPayments = SoapUtils::parseRow($downPayments, function ($item) {
                    return [
                        'amount' => $item['PESINATTUTARI'],
                    ];
                });

                foreach ($downPayments as $downPayment) {
                    $installments = $service->installmentOptions(join(',', [$this->product->stock_code, $downPayment['amount']]));

                    if (!$installments) {
                        LoggerService::logInfo(LogChannelsEnum::UpdatePaymentPlansForProduct, 'No $installments', ['stock_code' => $this->product->stock_code, 'dp' => $downPayment['amount']]);

                        continue;
                    }

                    if (!property_exists($installments, 'ROW')) {
                        LoggerService::logInfo(LogChannelsEnum::UpdatePaymentPlansForProduct, 'No ROW for installments', ['stock_code' => $this->product->stock_code, 'dp' => $downPayment['amount']]);

                        continue;
                    }

                    $installmentsObject = collect($installments)['ROW'];

                    $installmentsObject = json_decode(json_encode($installmentsObject), true, flags: JSON_OBJECT_AS_ARRAY);

                    $isSingleEntity = array_key_exists('SENETSAYISI', $installmentsObject) || array_key_exists('SENETTUTARI', $installmentsObject);

                    $installments = SoapUtils::parseRow($installments, function ($item) {
                        return [
                            'installments' => $item['SENETSAYISI'],
                            'monthly_payment' => $item['SENETTUTARI']
                        ];
                    }, $isSingleEntity);

                    if ($isSingleEntity) {
                        $productPlans->push([
                            'down_payment' => $downPayment['amount'],
                            'installments' => [$installments]
                        ]);
                    } else {
                        $productPlans->push([
                            'down_payment' => $downPayment['amount'],
                            'installments' => $installments
                        ]);
                    }
                }
            }

            foreach ($productPlans as $plan) {
                $downPayment = $this->product->downPayments()->create([
                    'amount' => $plan['down_payment']
                ]);

                if (isset($plan['installments'])) {
                    foreach ($plan['installments'] as $planInstallment) {
                        $downPayment->installmentOptions()->create([
                            'installments' => $planInstallment['installments'],
                            'monthly_payment' => $planInstallment['monthly_payment']
                        ]);
                    }
                }
            }
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::UpdatePaymentPlansForProduct, 'Error in handler', ['stock_code' => $this->product->stock_code, 'e' => $e]);

            $this->fail();

            return;
        }
    }
}
