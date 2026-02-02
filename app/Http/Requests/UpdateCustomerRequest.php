<?php

/**
 * File: app/Http/Requests/UpdateCustomerRequest.php
 * Purpose: Validation rules for updating an existing customer
 * Dependencies: Illuminate\Foundation\Http\FormRequest
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
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
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $this->route('customer')->id,
            'phone' => 'nullable|string|max:20',
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Navn er påkrevd.',
            'email.email' => 'E-post må være en gyldig e-postadresse.',
            'email.unique' => 'Denne e-postadressen er allerede i bruk.',
        ];
    }

    /**
     * Validate that at least one contact method is provided
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (empty($this->email) && empty($this->phone)) {
                $validator->errors()->add('email', 'Du må oppgi minst e-post eller telefonnummer.');
                $validator->errors()->add('phone', 'Du må oppgi minst e-post eller telefonnummer.');
            }
        });
    }
}

/**
 * Summary: Form Request for customer updates with Norwegian error messages and contact method validation
 */
