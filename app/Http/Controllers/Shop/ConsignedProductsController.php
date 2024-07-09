<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\ConsignedProduct;
use App\Models\ProductVariation;
use Exception;
use Illuminate\Http\Request;
use Jackiedo\Cart\Facades\Cart;

class ConsignedProductsController extends Controller
{
    public function consignedProducts(Request $request)
    {
        try {
            if (!config('app.consigned_enabled')) {
                return redirect()->route('panel')->with('error', __('web.consigned-disabled'));
            }

            $shop = auth()->user();

            $consignedProducts = $shop->consignedProducts()->with(['productVariation.color', 'productVariation.product'])->orderByDesc('id')->get();

            if ($request->input('search')) {
                $searchTerm = $request->input('search');

                $consignedProducts = $consignedProducts->filter(function ($item) use ($searchTerm) {
                    $colorMatches = false;
                    $productMatches = false;

                    if (isset($item->productVariation->color)) {
                        $color = $item->productVariation->color;
                        $colorMatches = str_contains(strtolower($color->erp_color_name), strtolower($searchTerm))
                            || (isset($color->currentTranslation) && str_contains(strtolower($color->currentTranslation->color_name), strtolower($searchTerm)));
                    }

                    if (isset($item->productVariation->product)) {
                        $product = $item->productVariation->product;
                        $productMatches = str_contains(strtolower($product->stock_code), strtolower($searchTerm))
                            || (isset($product->currentTranslation) && str_contains(strtolower($product->currentTranslation->product_name), strtolower($searchTerm)));
                    }

                    return str_contains(strtolower($item->chasis_no), strtolower($searchTerm)) || $colorMatches || $productMatches;
                });
            }

            return view('shop.products.index')->with([
                'products' => $consignedProducts->paginate(12)
            ]);
        } catch (Exception $e) {
            report($e);

            return redirect()->route('panel')->with('error', 'Error occured');
        }
    }

    public function buyProduct(Request $request, $consignedProductId)
    {
        try {
            if (!config('app.consigned_enabled')) {
                return redirect()->route('panel')->with('error', __('web.consigned-disabled'));
            }

            $product = ConsignedProduct::findOrFail((int)$consignedProductId);

            if (!$product->in_stock) {
                $cart = Cart::name('current');
                $cart->clearItems();

                return back()->with('error', __('web.product-is-out-of-stock'));
            }

            $variation = $product->productVariation;

            if (!$variation) {
                $cart = Cart::name('current');
                $cart->clearItems();

                return back()->with('error', 'Error occured. Contact the administration.');
            }

            $cart = Cart::name('current');

            $cart->clearItems();

            $cart->addItem([
                'model' => $variation,
                'quantity' => 1,
                'extra_info' => [
                    'consignedProductId' => $consignedProductId
                ]
            ]);

            return redirect()->route('cart');
        } catch (Exception $e) {
            report($e);

            return back()->with('error', 'Error occured. Contact the administration.');
        }
    }
}
