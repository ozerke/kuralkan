<?php

namespace App\Http\Controllers;
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\ChatBotOrderRequest;
use App\Http\Requests\API\ChatBotProductPriceRequest;
use App\Services\ChatBotApiService;
use Exception;


use Illuminate\Http\Request;

class ChatBotApiController extends Controller
{

    private $apiService;

    public function __construct(ChatBotApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function getOrderInfo(ChatBotOrderRequest $request)
    {
        try {
            return response()->json($this->apiService->getOrderInfo($request), 200);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getProductPrice(ChatBotProductPriceRequest $request)
    {
        try {
            return response()->json($this->apiService->getProductPriceByModel($request), 200);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

}
