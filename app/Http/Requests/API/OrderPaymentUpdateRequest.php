<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class OrderPaymentUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order_no' => 'required|exists:orders,order_no',
            'erp_order_id' => 'required',
            'payment_ref_no' => 'nullable|string',
            'approved_by_erp' => 'required|boolean',
            'amount_received' => 'required|numeric',
            'e_bond_no' => 'nullable|string'
        ];
    }
}
