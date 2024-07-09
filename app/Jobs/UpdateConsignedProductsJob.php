<?php

namespace App\Jobs;

use App\Models\Color;
use App\Models\Product;
use App\Models\User;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateConsignedProductsJob implements ShouldQueue
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
            LoggerService::logInfo(LogChannelsEnum::UpdateConsignedProducts, 'Consigned Products', ['products' => $this->productsCollection->toArray()]);

            $groupedCollection = $this->productsCollection->groupBy('erp_user_id');

            foreach ($groupedCollection as $erpShopId => $consignedProducts) {
                $salesPoint = User::where('erp_user_id', $erpShopId)->first();

                if ($salesPoint) {
                    if (!$salesPoint->isShopOrService()) {
                        LoggerService::logError(LogChannelsEnum::UpdateConsignedProducts, "Error in handler (Shop loop): User not a shop: {$erpShopId}");
                        continue;
                    }

                    foreach ($consignedProducts as $consignedProduct) {

                        $product = Product::where('stock_code', $consignedProduct['stock_code'])->first();
                        $color = Color::where('color_code', $consignedProduct['color_code'])->first();

                        if (!$product) {
                            LoggerService::logError(LogChannelsEnum::UpdateConsignedProducts, "Error in handler (Shop loop): Product not found: {$consignedProduct['stock_code']}");
                            continue;
                        }

                        if (!$color) {
                            LoggerService::logError(LogChannelsEnum::UpdateConsignedProducts, "Error in handler (Shop loop): Color not found: {$consignedProduct['color_code']}");
                            continue;
                        }

                        $variation = $product->variations()->where('color_id', $color->id)->first();

                        if ($variation) {
                            $salesPoint->addConsignedProductForShop(
                                $variation->id,
                                $consignedProduct['chasis_no'],
                                $consignedProduct['in_stock']
                            );
                        } else {
                            LoggerService::logError(LogChannelsEnum::UpdateConsignedProducts, "Error in handler (Shop loop): Variation not found", [
                                'stock_code' => $consignedProduct['stock_code'],
                                'color_code' => $consignedProduct['color_code']
                            ]);
                        }
                    }
                } else {
                    LoggerService::logError(LogChannelsEnum::UpdateConsignedProducts, "Error in handler (Shop loop): User not found (user ERP ID: {$erpShopId})");
                }
            }

            LoggerService::logSuccess(LogChannelsEnum::UpdateConsignedProducts, "Consigned Products");

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            LoggerService::logError(LogChannelsEnum::UpdateConsignedProducts, "Error in handler", ['e' => $e]);

            $this->fail();
        }
    }
}
