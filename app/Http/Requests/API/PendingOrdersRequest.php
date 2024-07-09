<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class PendingOrdersRequest extends FormRequest
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
        $withConfirmed = $this->withConfirmed;
        $from = $this->from;
        $to = $this->to;

        $this->merge([
            'withConfirmed' => $withConfirmed ? 1 : 0,
            'from' => !empty($from) ? $from : null,
            'to' => !empty($to) ? $to : null,
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
            'from' => 'date_format:d-m-Y|nullable',
            'to' => 'date_format:d-m-Y|nullable',
            'withConfirmed' => 'boolean|nullable'
        ];
    }
}
