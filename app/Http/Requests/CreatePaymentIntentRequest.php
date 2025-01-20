<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentIntentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cart' => 'required|array',
            'cart.*.id' => 'required|string',
            'cart.*.quantity' => 'required|integer|min:1',
            'cart.*.price' => 'required|numeric|min:0.50',
            'idempotencyKey' => 'required|string|max:255'
        ];
    }

    public function messages(): array
    {
        return [
            'cart.required' => 'Cart information is required',
            'cart.*.id.required' => 'Each cart item must have an ID',
            'cart.*.quantity.required' => 'Each cart item must have a quantity',
            'cart.*.quantity.min' => 'Quantity must be at least 1',
            'cart.*.price.required' => 'Each cart item must have a price',
            'cart.*.price.min' => 'Price must be at least $0.50',
            'idempotencyKey.required' => 'Idempotency key is required to prevent duplicate payments'
        ];
    }
}