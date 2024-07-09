<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class OrderUpdateRequest extends FormRequest
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
            'erp_user_id' => 'required',
            'erp_order_status' => 'nullable',
            'chasis_no' => 'nullable',
            'engine_no' => 'nullable',
            'invoice_link' => 'url|nullable',
            'temprorary_licence_doc_link' => 'url|nullable',
            'plate_printing_doc_link' => 'url|nullable',
            'delivery_date' => 'date_format:d-m-Y|nullable',
            'total_amount' => 'nullable|numeric'
        ];
    }
}
