<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "erp_order_id" => $this->erp_order_id,
            "erp_prefix" => $this->erp_prefix,
            "order_no" => $this->order_no,
            "invoice_user_no" => $this->invoiceUser->user_no,
            "service_point" => $this->deliveryUser->erp_user_id,
            "product_stock_code" => $this->productVariation->product->stock_code,
            "product_name" => $this->product_name,
            "product_variant_key" => $this->productVariation->variant_key,
            "product_color_code" => $this->productVariation->color->color_code,
            "total_amount" => (float)$this->total_amount,
            "chasis_no" => $this->chasis_no,
            "order_date" => $this->created_at->format('d-m-Y H:i'),
            "customer_name" =>  $this->invoiceUser->isCompany() ? $this->invoiceUser->company_name : $this->invoiceUser->site_user_name,
            "customer_surname" => $this->invoiceUser->isCompany() ? '' : $this->invoiceUser->site_user_surname,
            "customer_address" => $this->invoiceUser->address,
            "customer_district" => $this->invoiceUser->district->erp_district_name,
            "customer_city" => $this->invoiceUser->district->city->erp_city_name,
            "customer_phone" => $this->invoiceUser->phone,
            "customer_email" => $this->invoiceUser->email,
            "customer_national_id" => $this->invoiceUser->isCompany() ? '' : $this->invoiceUser->national_id,
            "customer_tax_id" => $this->invoiceUser->isCompany() ? $this->invoiceUser->tax_id : '',
            "customer_tax_office" => $this->invoiceUser->isCompany() ? $this->invoiceUser->tax_office : '',
            "payment_type" => $this->payment_type,
            "sales_point" => $this->user->id !== $this->invoiceUser->id ? $this->user->erp_user_id : null,
        ];
    }
}
