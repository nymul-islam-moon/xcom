<?php

namespace App\Http\Requests\Admin\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'name'                  => 'name',
            'email'                 => 'email address',
            'phone'                 => 'phone number',
            'status'                => 'status',
            'email_verified_at'     => 'email verified at',
            'password'              => 'password',
            'password_confirmation' => 'password confirmation',
        ];
    }

    public function rules(): array
    {
        // Route-model binding is Admin $user in your controller:
        // public function update(UpdateAdminRequest $request, Admin $user)
        $admin = $this->route('user'); // instance of App\Models\Admin

        return [
            'name'  => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('admins', 'email')->ignore($admin?->id),
            ],

            'phone' => [
                'sometimes',        // only validate if present
                'nullable',
                'string',
                'max:20',
                'regex:/^[0-9+\-\s\(\)]+$/',
                Rule::unique('admins', 'phone')
                    ->ignore($admin?->id)
                    ->whereNotNull('phone'),
            ],

            // You’re posting 0/1 from Blade; controller maps to 'active'/'inactive'
            'status' => ['sometimes', 'string', Rule::in(['active','inactive','suspended','pending'])],


            'email_verified_at' => ['sometimes', 'nullable', 'date'],

            // Optional on update; only changes if provided and confirmed
            'password' => ['sometimes', 'nullable', 'confirmed', Password::defaults()],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'          => 'This email address is already registered.',
            'phone.unique'          => 'This phone number is already in use.',
            'password.confirmed'    => 'The password confirmation does not match.',
            'phone.regex'           => 'The phone number may contain digits, spaces, parentheses, plus or hyphen.',
        ];
    }
}
