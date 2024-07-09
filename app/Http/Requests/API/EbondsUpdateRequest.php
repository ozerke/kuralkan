<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class EbondsUpdateRequest extends FormRequest
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
            "*.erp_order_id" => "required",
            "*.e_bond_no" => "required",
            "*.bond_amount" => "required|numeric|min:0",
            "*.bond_description" => "nullable|string",
            "*.due_date" => "date_format:d-m-Y|required",
            "*.is_penalty" => "nullable|boolean"
        ];
    }
}
