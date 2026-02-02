<?php

/**
 * File: app/Http/Requests/StoreCustomerRequest.php
 * Purpose: Validation rules for creating a new customer
 * Dependencies: Illuminate\Foundation\Http\FormRequest
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
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
            'username' => 'nullable|string|max:255|unique:users|regex:/^[a-zA-Z0-9_]+$/',
            'email' => 'nullable|string|email|max:255|unique:users',
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
            'username.unique' => 'Dette brukernavnet er allerede i bruk.',
            'username.regex' => 'Brukernavn kan kun inneholde bokstaver, tall og understrek.',
            'email.email' => 'E-post må være en gyldig e-postadresse.',
            'email.unique' => 'Denne e-postadressen er allerede i bruk.',
        ];
    }

    /**
     * Validate that at least username or contact method is provided
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (empty($this->username) && empty($this->email) && empty($this->phone)) {
                $validator->errors()->add('username', 'Du må oppgi minst brukernavn, e-post eller telefonnummer.');
            }
        });
    }
}

/**
 * Summary: Form Request for customer creation with Norwegian error messages and contact method validation
 */
