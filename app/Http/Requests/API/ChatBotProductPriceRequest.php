<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class ChatBotProductPriceRequest extends FormRequest
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
        $id = $this->id;

        $this->merge([ 
            'id' => !empty($id)  ? $id : null
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
            "id" => "numeric|nullable"
        ];
    }
}
