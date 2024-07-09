<?php

namespace App\Jobs;

use App\Models\Language;
use App\Models\Product;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UpdateProductSpecsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $specsCollection;
    /**
     * Create a new job instance.
     */
    public function __construct($specsCollection)
    {
        $this->onQueue('erpjobs');

        $this->specsCollection = $specsCollection;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::beginTransaction();

        try {
            LoggerService::logInfo(LogChannelsEnum::UpdateProductsSpecs, 'Products Specs', ['productSpecs' => $this->specsCollection->toArray()]);

            $collection = $this->specsCollection->groupBy('stock_code');

            foreach ($collection as $stockCode => $specifications) {
                $product = Product::where('stock_code', $stockCode)->first();

                if ($product) {
                    foreach ($specifications as $specification) {
                        $existing = $product->specifications()->where([
                            ['lang_id', Language::AVAILABLE['tr']],
                            ['display_order', $specification['display_order']]
                        ])->first();

                        if ($existing) {
                            $existing->update([
                                'value' => is_array($specification['value']) ? "" : $specification['value'],
                                'specification' => $specification['specification']
                            ]);
                        } else {
                            $product->specifications()->create([
                                'lang_id' => Language::AVAILABLE['tr'],
                                'specification' => $specification['specification'],
                                'value' => is_array($specification['value']) ? "" : $specification['value'],
                                'display_order' => $specification['display_order']
                            ]);
                        }

                        $translation =  $product->specifications()->where([
                            ['lang_id', Language::AVAILABLE['en']],
                            ['display_order', $specification['display_order']]
                        ])->first();

                        if (!$translation) {
                            $product->specifications()->create([
                                'lang_id' => Language::AVAILABLE['en'],
                                'specification' => '',
                                'value' => '',
                                'display_order' => $specification['display_order']
                            ]);
                        }
                    }
                }
            }

            LoggerService::logSuccess(LogChannelsEnum::UpdateProductsSpecs, 'Products Specs');

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            LoggerService::logError(LogChannelsEnum::UpdateProductsSpecs, 'Error in handler', ['e' => $e]);

            $this->fail();
        }
    }
}
