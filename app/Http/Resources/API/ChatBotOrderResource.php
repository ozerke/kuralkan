<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatBotOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $order_status = $this->latest_order_status_id;
        switch ($order_status) {
            case 3:
                if (empty($this->delivery_date) || $this->delivery_date < date('Y-m-d')) {
                    $order_status = 31;
                } else {
                    $order_status = 30;
                }
                break;
            case 4:
                if (!empty($this->plate_printing_doc_link) && !empty($this->temprorary_licence_doc_link)) {
                    $order_status = 40;
                } else if (!empty($this->invoice_link)) {
                    $order_status = 41;
                } else {
                    $order_status = 42;
                }
                break;
        }


        return [

            "erp_order_id" => $this->erp_order_id,
            "erp_prefix" => $this->erp_prefix,
            "order_no" => $this->order_no,
            "service_point" => $this->deliveryUser->erp_user_name . "\n" . $this->deliveryUser->address . $this->deliveryUser->district->currentTranslation->district_name . " " . $this->deliveryUser->district->city->currentTranslation->city_name . " Tel:" . $this->deliveryUser->phone,
            "product_stock_code" => $this->productVariation->product->stock_code,
            "product_name" => $this->product_name,
            "product_variant_key" => $this->productVariation->variant_key,
            "product_color_code" => $this->productVariation->color->color_code,
            "total_amount" => (float)$this->total_amount,
            "order_date" => $this->created_at->format('d-m-Y H:i'),
            "customer_name" =>  $this->invoiceUser->site_user_name,
            "customer_surname" => $this->invoiceUser->site_user_surname,
            "customer_surname" => $this->invoiceUser->phone,
            "customer_address" => $this->invoiceUser->address,
            "customer_district" => $this->invoiceUser->district->erp_district_name,
            "customer_city" => $this->invoiceUser->district->city->erp_city_name,
            "customer_phone" => $this->invoiceUser->phone,
            "customer_email" => $this->invoiceUser->email,
            "customer_national_id" => $this->invoiceUser->national_id,
            "customer_tax_id" => $this->invoiceUser->tax_id,
            "customer_tax_office" => $this->invoiceUser->tax_office,
            "payment_type" => $this->payment_type,
            "sales_point" => $this->user->id !== $this->invoiceUser->id ? $this->user->erp_user_id : null,
            "chasis_no" => $this->chasis_no,
            "motor_no" => $this->motor_no,
            "invoice_link" => $this->invoice_link,
            "temprorary_licence_doc_link" => $this->temprorary_licence_doc_link,
            "plate_printing_doc_link" => $this->plate_printing_doc_link,
            "status" => $order_status,
            "response_string" => "Sipariş No: " . $this->order_no . "\n" . $this->product_name . "\nDurum: " .
                $this->latest_status->orderStatus->currentTranslation->status,
            "delivery_date" => date('d.m.Y', strtotime($this->delivery_date)),
            "remaining_amount" => $this->getOrderPaymentsState()['remaining_amount']
        ];
    }
}
