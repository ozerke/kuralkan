<?php

namespace App\Jobs;

use App\Models\Color;
use App\Models\Product;
use App\Models\ShopStock;
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

class UpdateSalesPointsStocksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $stocksCollection;

    /**
     * Create a new job instance.
     */
    public function __construct($stocksCollection)
    {
        $this->onQueue('erpjobs');

        $this->stocksCollection = $stocksCollection;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::beginTransaction();

        try {
            LoggerService::logInfo(LogChannelsEnum::UpdateSalesPointsStocks, 'Update Sales Points Stocks', ['stocks' => $this->stocksCollection->toArray()]);

            $groupedCollection = $this->stocksCollection->groupBy('erp_user_id');

            ShopStock::where('id', '>', 0)->delete();

            foreach ($groupedCollection as $erpShopId => $shopStocks) {
                $salesPoint = User::where('erp_user_id', $erpShopId)->first();

                if ($salesPoint) {
                    $salesPoint->shopStocks()->delete();

                    foreach ($shopStocks as $shopStock) {

                        $product = Product::where('stock_code', $shopStock['stock_code'])->first();
                        $color = Color::where('color_code', $shopStock['color_code'])->first();

                        if (!$product || !$color) {
                            LoggerService::logError(LogChannelsEnum::UpdateSalesPointsStocks, "Product or color not found (color_code: {$shopStock['color_code']}, stock_code: {$shopStock['stock_code']})");
                        } else {
                            $variation = $product->variations()->where('color_id', $color->id)->first();

                            if ($variation) {
                                $salesPoint->shopStocks()->create([
                                    'product_id' => $product->id,
                                    'product_variation_id' => $variation->id,
                                    'stock' => $shopStock['stock']
                                ]);
                            }
                        }
                    }
                } else {
                    LoggerService::logError(LogChannelsEnum::UpdateSalesPointsStocks, "User not found (user ERP ID: {$erpShopId})");
                }
            }

            LoggerService::logSuccess(LogChannelsEnum::UpdateSalesPointsStocks, "Update Sales Points Stocks");

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            LoggerService::logError(LogChannelsEnum::UpdateSalesPointsStocks, 'Error in handler', ['e' => $e]);

            $this->fail();
        }
    }
}
