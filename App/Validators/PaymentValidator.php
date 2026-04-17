<?php

namespace App\Validators;

class PaymentValidator
{
    /**
     * Create payment validation rules
     */
    public static function create(): array
    {
        return [
            'invoice' => 'required|string|alphanumeric|max:20',
            'phone' => 'required|string|min:11|max:11',
            'amount' => 'required|numeric',
            'callbackUrl' => 'required|url',
        ];
    }

    /**
     * Custom error messages
     */
    public static function messages(): array
    {
        return [
            'invoice.required' => 'Invoice is required.',
            'invoice.string' => 'Invoice must be string.',
            'invoice.alphanumeric' => 'Invoice must be alphanumeric.',
            'invoice.max' => 'Invoice must not exceed 20 characters.',
            'phone.required' => 'Phone is required.',
            'phone.string' => 'Phone must be a string.',
            'phone.min' => 'Phone must be at least 11 characters.',
            'phone.max' => 'Phone must not exceed 11 characters.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a number.',
            'callbackUrl.required' => 'Callback URL is required.',
            'callbackUrl.url' => 'Callback URL must be a valid URL.',
        ];
    }
}
