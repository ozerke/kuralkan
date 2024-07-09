<?php

namespace App\Jobs;

use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariation;
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

class UpdateProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $productsCollection;
    /**
     * Create a new job instance.
     */
    public function __construct($productsCollection)
    {
        $this->onQueue('erpjobs');

        $this->productsCollection = $productsCollection;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::beginTransaction();

        try {
            LoggerService::logInfo(LogChannelsEnum::UpdateProducts, 'Products', ['products' => $this->productsCollection->toArray()]);

            foreach ($this->productsCollection as $erpVariation) {

                $product = Product::where('stock_code', $erpVariation['stock_code'])->first();
                $color = Color::where('color_code', $erpVariation['color_code'])->first();

                if (!$product) {
                    $product = Product::createNonExistingProduct($erpVariation);
                }

                if (!$color) {
                    $color = Color::createNonExistingColor($erpVariation);
                }

                $variation = ProductVariation::where([
                    ['product_id', $product->id],
                    ['color_id', $color->id],
                    ['variant_key', $erpVariation['variant_key']]
                ])->first();

                if (!$variation) {
                    $variation = ProductVariation::createNonExistingVariation($erpVariation, $product->id, $color->id);
                } else {
                    $date = $erpVariation['estimated_delivery_date'];

                    if ($date) {
                        $date = explode('-', $date);
                        $date = $date[2] . '-' . $date[1] . '-' . $date[0];
                    }

                    $variation->update([
                        'price' => $erpVariation['price'],
                        'total_stock' => $erpVariation['total_stock'],
                        'estimated_delivery_date' => $date,
                        'otv_ratio' => $erpVariation['otv'],
                        'vat_ratio' => $erpVariation['vat'],
                    ]);
                }
            }

            LoggerService::logSuccess(LogChannelsEnum::UpdateProducts, 'Products');

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            LoggerService::logError(LogChannelsEnum::UpdateProducts, 'Error in handler', ['e' => $e]);

            $this->fail();
        }
    }
}
