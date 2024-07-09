<?php
namespace App\Services;

use App\Http\Requests\API\ChatBotOrderRequest;
use App\Http\Resources\API\ChatBotOrderResource;
use App\Http\Requests\API\ChatBotProductPriceRequest;
use App\Http\Resources\API\ChatBotProductPriceResource;

use App\Models\Bank;
use App\Models\LegalRegistration;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\OrderStatus;
use App\Models\ProductVariation;
use Carbon\Carbon;
use Exception;

class ChatBotApiService
{
    public function __construct()
    {
        $phone = "";
    }

 
    public function getOrderInfo(ChatBotOrderRequest $request)
    {
        $data = $request->validated();


        if(!empty($data['phone'])) 
        {

            $this->phone = '+'.$data['phone'];

            $orderQuery = Order::orderBy('id', 'desc')->limit(1)->whereHas('invoiceUser', function($q) { $q->where('phone', $this->phone);})->with([
                'invoiceUser.district.city',
                'deliveryUser',
                'user',
                'productVariation.product',
                'productVariation.color',
                'invoiceUser'

            ])->get();
            


            return ChatBotOrderResource::collection($orderQuery);
        }
    }

    public function getProductPriceByModel(ChatBotProductPriceRequest $request)
    {
        $data = $request->validated();
        if(!empty($data['id']))
        {
            $this->id = $data['id'];
            $priceQuery = ProductVariation::where('id', $this->id)->get();
            return ChatBotProductPriceResource::collection($priceQuery);
        }
    }


}
