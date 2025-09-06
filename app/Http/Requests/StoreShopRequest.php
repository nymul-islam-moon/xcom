<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Shop Info
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:shops,email'],
            'phone' => ['nullable', 'string', 'max:15'],
            // 'website_url' => ['nullable', 'url', 'max:255'], // Uncomment if needed

            // Shopkeeper
            'shop_keeper_name' => ['required', 'string', 'max:255'],
            'shop_keeper_phone' => ['required', 'string', 'max:15'],
            'shop_keeper_email' => ['nullable', 'email', 'max:255'],
            'shop_keeper_nid' => ['required', 'string', 'max:50'],
            'shop_keeper_tin' => ['required', 'string', 'max:50'],
            'dbid' => ['nullable', 'string', 'max:50'],
            'shop_keeper_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'shop_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],

            // Bank
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_account_number' => ['nullable', 'string', 'max:100'],
            'bank_branch' => ['nullable', 'string', 'max:255'],

            // Address & Description
            'business_address' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],

            // Email verification datetime
            'email_verified_at' => ['nullable', 'date'],

            // Password
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
