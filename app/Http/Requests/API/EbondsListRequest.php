<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class EbondsListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $from = $this->from;
        $to = $this->to;
        $eBondNo = $this->e_bond_no;
        $erpOrderId = $this->erp_order_id;

        $this->merge([
            'from' => !empty($from) ? $from : null,
            'to' => !empty($to) ? $to : null,
            'e_bond_no' => !empty($eBondNo) ? $eBondNo : null,
            'erp_order_id' => !empty($erpOrderId) ? $erpOrderId : null,
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
            'erp_order_id' => 'nullable',
            'e_bond_no' => 'nullable',
            'from' => 'date_format:d-m-Y|nullable',
            'to' => 'date_format:d-m-Y|nullable'
        ];
    }
}
