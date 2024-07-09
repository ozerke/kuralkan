<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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
            "*.stock_code" => "required",
            "*.title" => "required",
            "*.color" => "required",
            "*.color_code" => "required",
            "*.price" => "required|numeric|min:0",
            "*.total_stock" => "required|numeric|min:0",
            "*.vat" => "required|numeric",
            "*.otv" => "required|numeric",
            "*.estimated_delivery_date" => "date_format:d-m-Y|required",
            "*.variant_key" => "required"
        ];
    }
}
