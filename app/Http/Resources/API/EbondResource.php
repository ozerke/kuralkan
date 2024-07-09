<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EbondResource extends JsonResource
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
            "e_bond_no" => $this->e_bond_no,
            "bond_amount" => (float)$this->bond_amount,
            "remaining_amount" => (float)$this->remaining_amount,
            "bond_description" => $this->bond_description,
            "due_date" => $this->due_date->format('d-m-Y'),
            "is_penalty" => (bool) $this->penalty,
            "paid" => $this->isPaid()
        ];
    }
}
