<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Exception;
use Illuminate\Http\Request;

class OpenDataController extends Controller
{
    public function getProducts()
    {
        try {
            $products = Product::displayable()->get();

            $products = $products->map(function ($product) {
                return [
                    'title' => $product->currentTranslation->product_name,
                    'stock_code' => $product->stock_code
                ];
            });

            return response()->json([
                'products' => $products
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ApplicationApi, 'OpenData API getProducts', ['e' => $e]);

            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getProduct(Request $request)
    {
        try {
            if (!$request->input('stock_code')) {
                return response()->json(['message' => 'No stock_code was specified']);
            }
            $product = Product::where('stock_code', $request->input('stock_code'))->first();

            if (!$product) {
                return response()->json(['message' => 'Not found'], 404);
            }

            $product = [
                'title' => $product->currentTranslation->product_name,
                'stock_code' => $product->stock_code,
                'variations' => $product->getVariationsBasicData()
            ];

            return response()->json([
                'product' => $product
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ApplicationApi, 'OpenData API getProduct', ['e' => $e]);

            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
