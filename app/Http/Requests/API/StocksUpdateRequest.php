<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class StocksUpdateRequest extends FormRequest
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
            '*.erp_user_id' => 'required',
            '*.erp_user_name' => 'required',
            '*.stock_code' => 'required',
            '*.title' => 'required',
            '*.color' => 'required',
            '*.color_code' => 'required',
            '*.stock' => 'required|numeric',
        ];
    }
}
