<?php

/**
 * File: app/Http/Requests/StorePaymentRequest.php
 * Purpose: Validation rules for registering a payment
 * Dependencies: Illuminate\Foundation\Http\FormRequest
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|integer|min:1',
            'paid_at' => 'required|date',
            'payment_method' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'Beløp er påkrevd.',
            'amount.integer' => 'Beløp må være et tall (i øre).',
            'amount.min' => 'Beløp må være minst 1 øre.',
            'paid_at.required' => 'Betalingsdato er påkrevd.',
            'paid_at.date' => 'Betalingsdato må være en gyldig dato.',
        ];
    }
}

/**
 * Summary: Form Request for payment registration with Norwegian error messages
 */
