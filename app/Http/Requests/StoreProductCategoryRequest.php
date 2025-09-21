<?php

namespace App\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StoreProductCategoryRequest extends FormRequest
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
            'name'        => 'category name',
            'description' => 'description',
            'is_active'   => 'status',
        ];
    }

    /**
     * Prepare data before validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'name'      => Str::ucfirst(Str::lower(trim($this->input('name')))),
            'slug'      => Str::slug(trim($this->input('name'))),
            'is_active' => $this->boolean('is_active'), // Laravel 11 boolean helper
        ]);
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        $categoryId = $this->route('category') ?? null; // for update requests

        return [
            'name' => ['required', 'string', 'max:255', 'unique:product_categories,name' . ($categoryId ? ",$categoryId" : '')],
            'slug' => ['required', 'string', 'max:255', 'unique:product_categories,slug' . ($categoryId ? ",$categoryId" : '')],
            'is_active' => ['required', 'boolean'], // boolean covers true/false (1/0)
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * Custom messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter a :attribute.',
            'name.unique'   => 'The :attribute ":input" is already in use.',
            'name.max'      => 'The :attribute may not be greater than :max characters.',
            'is_active.required' => 'Please select a :attribute.',
            'is_active.boolean'  => 'The selected :attribute is invalid.',
        ];
    }

    /**
     * Handle failed validation.
     */
    protected function failedValidation(Validator $validator)
    {
        // Log all errors
        Log::error('Product Category validation failed', [
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
