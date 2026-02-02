<?php

/**
 * File: app/Http/Requests/StoreItemRequest.php
 * Purpose: Validation rules for creating a new item
 * Dependencies: Illuminate\Foundation\Http\FormRequest
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'purchase_price' => 'nullable|integer|min:0',
            'target_price' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Navn er påkrevd.',
            'purchase_price.integer' => 'Innkjøpspris må være et tall (i øre).',
            'purchase_price.min' => 'Innkjøpspris kan ikke være negativ.',
            'target_price.integer' => 'Målpris må være et tall (i øre).',
            'target_price.min' => 'Målpris kan ikke være negativ.',
        ];
    }
}

/**
 * Summary: Form Request for item creation with Norwegian error messages
 */
