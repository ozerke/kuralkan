<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderPaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "order_no" => $this->order->order_no,
            "erp_order_id" => $this->order->erp_prefix . $this->order->erp_order_id,
            "erp_user_id" => $this->order->invoiceUser->erp_user_id,
            "payment_ref_no" => $this->payment_ref_no,
            "payment_amount" => (float)$this->payment_amount,
            "collected_payment" => (float)$this->collected_payment,
            "payment_type" => $this->payment_type,
            "bank_name" => $this->bankAccount ? $this->bankAccount->bank->erp_bank_name : null,
            "number_of_installments" => $this->number_of_installments,
            'seen_by_erp' => (bool) $this->seen_by_erp,
            'application_fee' => (bool) $this->is_fee_payment,
            'date' => $this->created_at->format('d-m-Y H:i'),
            'e_bond_no' => $this->e_bond_no
        ];
    }
}
