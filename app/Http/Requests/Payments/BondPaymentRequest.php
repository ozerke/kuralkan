<?php

namespace App\Http\Requests\Payments;

use Illuminate\Foundation\Http\FormRequest;

class BondPaymentRequest extends FormRequest
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
            'payment_amount' => 'in:full,partial|required',
            'custom_amount' => 'required_if:payment_amount,partial|nullable|numeric',
            'name' => 'required_if:payment_type,credit-card|nullable|string',
            'number' => 'required_if:payment_type,credit-card|nullable|string',
            'expiry' => 'required_if:payment_type,credit-card|nullable|string',
            'cvc' => 'required_if:payment_type,credit-card|nullable|numeric',
            'payment_type' => 'required|in:bank-transfer,credit-card',
            'selected_bank' => 'required_if:payment_type,bank-transfer|nullable|numeric',
            'number_of_installments' => 'required_if:payment_type,credit-card|nullable|numeric',
        ];
    }
}
