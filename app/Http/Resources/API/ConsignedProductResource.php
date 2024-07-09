<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsignedProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "erp_user_id" => $this->user->erp_user_id,
            "stock_code" => $this->productVariation->product->stock_code,
            "color_code" => $this->productVariation->color->color_code,
            "chasis_no" => $this->chasis_no
        ];
    }
}
