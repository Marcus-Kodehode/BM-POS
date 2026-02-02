<?php

/**
 * File: app/Http/Requests/StoreOrderLineRequest.php
 * Purpose: Validation rules for adding an order line
 * Dependencies: Illuminate\Foundation\Http\FormRequest
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderLineRequest extends FormRequest
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
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|integer|min:0',
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'item_id.required' => 'Du må velge en vare.',
            'item_id.exists' => 'Valgt vare finnes ikke.',
            'quantity.required' => 'Antall er påkrevd.',
            'quantity.min' => 'Antall må være minst 1.',
            'unit_price.required' => 'Enhetspris er påkrevd.',
            'unit_price.integer' => 'Enhetspris må være et tall (i øre).',
            'unit_price.min' => 'Enhetspris kan ikke være negativ.',
        ];
    }
}

/**
 * Summary: Form Request for order line creation with Norwegian error messages
 */
