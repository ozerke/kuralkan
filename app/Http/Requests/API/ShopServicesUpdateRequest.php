<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class ShopServicesUpdateRequest extends FormRequest
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
            "*.erp_user_id" => "required",
            "*.erp_user_name" => "required|string",
            "*.address" => "required",
            "*.phone" => "string|nullable",
            "*.email" => "string|nullable",
            "*.district" => "string|required",
            "*.city" => "string|required",
            "*.latitude" => "numeric",
            "*.longitude" => "numeric",
            "*.shop" => "boolean|required",
            "*.service" => "boolean|required",
        ];
    }
}
