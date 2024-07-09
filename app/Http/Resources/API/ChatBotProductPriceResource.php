<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatBotProductPriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * 
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // $values = print_r($this, false);
        // return $values;
        $response = [
            "product_name" => $this->product->currentTranslation->product_name,
            "price" => number_format(($this->price * (100 + $this->vat_ratio)) / 100, 0, ",", "."),
            'product_price' => round($this->price * (100 + $this->vat_ratio) / 100, 0),
            'price_wo_vat' => $this->price,
            "vat_ratio" => $this->vat_ratio

        ];
        return $response;
    }
}
