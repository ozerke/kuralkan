<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class ChatBotOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $phone = $this->phone;
        $order_no = $this->order_no;

        $this->merge([ 
            'phone' => $phone ? $phone : null,
            'order_no' => !empty($order_no) ? $order_no : null,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "phone" => "string|nullable",
            "order_no" => "string|nullable",
        ];
    }
}
