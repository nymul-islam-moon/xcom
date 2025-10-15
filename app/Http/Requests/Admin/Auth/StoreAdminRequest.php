<?php

namespace App\Http\Requests\Admin\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreAdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Human-friendly attribute names.
     */
    public function attributes(): array
    {
        return [
            'name' => 'name',
            'email' => 'email address',
            'phone' => 'phone number',
            'status' => 'status',
            'email_verified_at' => 'email verified at',
            'password' => 'password',
            'password_confirmation' => 'password confirmation',
        ];
    }

    /**
     * Validation rules for creating an admin.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('admins', 'email'),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[0-9+\-\s\(\)]+$/',
                // Only enforce uniqueness for non-null phones
                Rule::unique('admins', 'phone')->whereNotNull('phone'),
            ],
            'status' => ['nullable', 'boolean'],            // will be 1/0 after prepareForValidation
            'email_verified_at' => ['nullable', 'date'],    // optional pre-verified admins
            'password' => ['required', 'confirmed', Password::defaults()],
            // expects a matching 'password_confirmation' field
        ];
    }

    /**
     * Custom messages (optional).
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'This email address is already registered.',
            'phone.unique' => 'This phone number is already in use.',
            'password.confirmed' => 'The password confirmation does not match.',
            'phone.regex' => 'The phone number may contain digits, spaces, parentheses, plus or hyphen.',
        ];
    }
}
