<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\CCPaymentsListRequest;
use App\Http\Requests\API\ConsignedProductsListRequest;
use App\Http\Requests\API\EbondsListRequest;
use App\Http\Requests\API\EbondsUpdateRequest;
use App\Http\Requests\API\OrderPaymentUpdateRequest;
use App\Http\Requests\API\OrderUpdateRequest;
use App\Http\Requests\API\PendingOrdersRequest;
use App\Http\Requests\API\ProductSpecsRequest;
use App\Http\Requests\API\ProductUpdateRequest;
use App\Http\Requests\API\ShopServicesUpdateRequest;
use App\Http\Requests\API\StocksUpdateRequest;
use App\Http\Requests\API\UpdateConsignedProductsRequest;
use App\Services\ERPApiService;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use App\Utils\ApiUtils;
use Exception;

class ERPApiController extends Controller
{
    private $apiService;

    public function __construct(ERPApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function getPendingOrders(PendingOrdersRequest $request)
    {
        try {
            return response()->json($this->apiService->getPendingOrders($request), 200);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::GetPendingOrders, 'getPendingOrders', ['e' => $e]);

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function updateOrder(OrderUpdateRequest $request)
    {
        return $this->apiService->updateOrder($request);
    }

    public function updatePayment(OrderPaymentUpdateRequest $request)
    {
        return $this->apiService->updatePayment($request);
    }

    public function getPaymentList(CCPaymentsListRequest $request)
    {
        try {
            return response()->json($this->apiService->getCCPaymentList($request), 200);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::GetCCPaymentList, 'getPaymentList', ['e' => $e]);

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function updateProducts(ProductUpdateRequest $request)
    {
        try {
            $productsCollection = collect($request->all());

            ApiUtils::validateJson($request);

            $this->apiService->updateProductList($productsCollection);

            return response()->json(['status' => true]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::UpdateProducts, 'updateProducts', ['e' => $e]);

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function updateTechnicalSpecs(ProductSpecsRequest $request)
    {
        try {
            $specsCollection = collect($request->all());

            ApiUtils::validateJson($request);

            $this->apiService->updateProductSpecifications($specsCollection);

            return response()->json(['status' => true]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::UpdateProductsSpecs, 'updateTechnicalSpecs', ['e' => $e]);

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function updateStocks(StocksUpdateRequest $request)
    {
        try {
            $stocksCollection = collect($request->all());

            ApiUtils::validateJson($request);

            $this->apiService->updateStocks($stocksCollection);

            return response()->json(['status' => true]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::UpdateSalesPointsStocks, 'updateStocks', ['e' => $e]);

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function updateShopServices(ShopServicesUpdateRequest $request)
    {
        try {
            $usersCollection = collect($request->all());

            ApiUtils::validateJson($request);

            $this->apiService->updateShopServices($usersCollection);

            return response()->json(['status' => true]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::UpdateSalesPoints, 'updateShopServices', ['e' => $e]);

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function updateConsignedProducts(UpdateConsignedProductsRequest $request)
    {
        try {
            $productsCollection = collect($request->all());

            ApiUtils::validateJson($request);

            $this->apiService->updateConsignedProducts($productsCollection);

            return response()->json(['status' => true]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::UpdateConsignedProducts, 'updateConsignedProducts', ['e' => $e]);

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getConsignedProductsList(ConsignedProductsListRequest $request)
    {
        try {
            return response()->json($this->apiService->getConsignedProductsList($request), 200);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::GetConsignedProductsList, 'getConsignedProductsList', ['e' => $e]);

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function updateEbonds(EbondsUpdateRequest $request)
    {
        try {
            $ebondsCollection = collect($request->all());

            ApiUtils::validateJson($request);

            $this->apiService->updateEbonds($ebondsCollection);

            return response()->json(['status' => true]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::UpdateEbonds, 'updateEbonds', ['e' => $e]);

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getEbondsList(EbondsListRequest $request)
    {
        try {
            return response()->json($this->apiService->getEbondsList($request), 200);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::EbondsList, 'getEbondsList', ['e' => $e]);

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
