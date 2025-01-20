<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreditTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'pack_id' => ['nullable', 'integer', 'exists:packs,id']
        ];
    }

    public function messages(): array
    {
        return [
            'amount.min' => 'The transaction amount must be positive.',
            'pack_id.exists' => 'The selected pack does not exist.'
        ];
    }
}