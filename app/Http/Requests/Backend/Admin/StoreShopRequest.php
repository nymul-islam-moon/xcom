<?php

namespace App\Http\Requests\Backend\Admin;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class StoreShopRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Stop validation on first failure.
     */
    protected $stopOnFirstFailure = true;

    /**
     * Custom attribute names (for prettier errors).
     */
    public function attributes(): array
    {
        return [
            'name'                  => 'shop name',
            'email'                 => 'shop email',
            'phone'                 => 'shop phone',
            'slug'                  => 'shop slug',
            'shop_keeper_name'      => 'shop keeper name',
            'shop_keeper_phone'     => 'shop keeper phone',
            'shop_keeper_nid'       => 'shop keeper nid',
            'shop_keeper_email'     => 'shop keeper email',
            'shop_keeper_photo'     => 'shop keeper photo',
            'shop_keeper_tin'       => 'shop keeper tin',
            'dbid'                  => 'digital business identification',
            'bank_name'             => 'bank name',
            'is_active'             => 'is active',
            'status'                => 'status',
            'bank_account_number'   => 'bank account number',
            'bank_branch'           => 'bank branch',
            'shop_logo'             => 'shop logo',
            'business_address'      => 'business address',
            'description'           => 'description',
            'password'              => 'password',
        ];
    }

    /**
     * Prepare data before validation.
     */
    protected function prepareForValidation()
    {
        $name = trim($this->input('name'));
        $this->merge([
            'name'                => Str::title(Str::lower($name)),
            'slug'                => Str::slug($name),
            'email'               => Str::lower(trim($this->input('email'))),
            'shop_keeper_email'   => Str::lower(trim($this->input('shop_keeper_email'))),
            'shop_keeper_name'    => Str::title(Str::lower(trim($this->input('shop_keeper_name')))),
            'is_active'           => $this->boolean('is_active'),
            'status'              => in_array($this->input('status'), ['pending', 'active', 'inactive', 'suspended'])
                ? $this->input('status')
                : 'pending',
            'password'            => $this->input('password') ? $this->input('password') : null,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Shop Info
            'name'                  => ['required', 'string', 'max:255', 'unique:shops,name'],
            'email'                 => ['required', 'email', 'max:255', 'unique:shops,email'],
            'slug'                  => ['required', 'string', 'max:255', 'unique:shops,slug'],
            'phone'                 => ['nullable', 'string', 'max:15'],

            // Shopkeeper
            'shop_keeper_name'      => ['required', 'string', 'max:255'],
            'shop_keeper_phone'     => ['required', 'string', 'max:15'],
            'shop_keeper_email'     => ['nullable', 'email', 'max:255'],
            'shop_keeper_nid'       => ['required', 'string', 'max:50'],
            'shop_keeper_tin'       => ['required', 'string', 'max:50'],
            'shop_keeper_photo'     => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],

            // Digital Business ID
            'dbid'                  => ['nullable', 'string', 'max:50'],

            // Bank
            'bank_name'             => ['nullable', 'string', 'max:255'],
            'bank_account_number'   => ['nullable', 'string', 'max:100'],
            'bank_branch'           => ['nullable', 'string', 'max:255'],

            // Shop Logo
            'shop_logo'             => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],

            // Address & Description
            'business_address'      => ['nullable', 'string'],
            'description'           => ['nullable', 'string'],

            // Verification and status
            'email_verified_at'     => ['nullable', 'date'],
            'is_active'             => ['nullable', 'boolean'],
            'status'                => ['required', 'in:pending,active,inactive,suspended'],

            // Password
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            // General
            'required'     => 'The :attribute field is required.',
            'string'       => 'The :attribute must be a valid string.',
            'max'          => 'The :attribute may not be greater than :max characters.',
            'min'          => 'The :attribute must be at least :min characters.',
            'email'        => 'The :attribute must be a valid email address.',
            'unique'       => 'The :attribute has already been taken.',
            'confirmed'    => 'The :attribute confirmation does not match.',
            'boolean'      => 'The :attribute field must be true or false.',
            'image'        => 'The :attribute must be an image.',
            'mimes'        => 'The :attribute must be a file of type: :values.',
            'date'         => 'The :attribute must be a valid date.',
            'in'           => 'The selected :attribute is invalid.',

            // Specific file size
            'shop_keeper_photo.max' => 'The shop keeper photo may not be greater than 2MB.',
            'shop_logo.max'         => 'The shop logo may not be greater than 2MB.',
        ];
    }

    /**
     * Handle failed validation.
     */
    protected function failedValidation(Validator $validator)
    {
        // Log all errors
        Log::error('Product Category Store validation failed', [
            'errors' => $validator->errors()->toArray(),
            'input'  => $this->all(),
        ]);

        throw new HttpResponseException(
            redirect()->back()
                ->withErrors($validator)
                ->withInput()
        );
    }
}
