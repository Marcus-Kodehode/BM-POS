<?php

/**
 * File: app/Http/Requests/StoreOrderRequest.php
 * Purpose: Validation rules for creating a new order
 * Dependencies: Illuminate\Foundation\Http\FormRequest
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'customer_id' => 'required|exists:users,id',
            'notes' => 'nullable|string',
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'customer_id.required' => 'Du må velge en kunde.',
            'customer_id.exists' => 'Valgt kunde finnes ikke.',
        ];
    }
}

/**
 * Summary: Form Request for order creation with Norwegian error messages
 */
