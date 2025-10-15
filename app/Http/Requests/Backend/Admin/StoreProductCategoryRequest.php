<?php

namespace App\Http\Requests\Backend\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
            'name' => 'category name',
            'is_active' => 'status',
            'slug' => 'slug',
            'description' => 'description',
        ];
    }

    /**
     * Prepare data before validation.
     */
    protected function prepareForValidation()
    {
        // Normalize name
        $name = Str::title(Str::lower(trim($this->input('name'))));

        // Build slug from normalized name
        $slug = Str::slug($name);

        $this->merge([
            'name' => $name,
            'slug' => $slug,
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        // Support both id or model bound via route('category')
        $routeCategory = $this->route('category');
        $categoryId = null;

        if ($routeCategory) {
            // If the route model is an Eloquent model, try to get its key
            if (is_object($routeCategory) && method_exists($routeCategory, 'getKey')) {
                $categoryId = $routeCategory->getKey();
            } else {
                $categoryId = $routeCategory;
            }
        }

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                // unique name (ignore current record on update)
                Rule::unique('product_categories', 'name')->ignore($categoryId),
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                // unique slug (ignore current record on update)
                Rule::unique('product_categories', 'slug')->ignore($categoryId),
            ],
            'is_active' => ['required', 'boolean'],
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
            'name.unique' => 'The :attribute ":input" is already in use.',
            'name.max' => 'The :attribute may not be greater than :max characters.',
            'is_active.required' => 'Please select a :attribute.',
            'is_active.boolean' => 'The selected :attribute is invalid.',
            'slug.required' => 'The :attribute is required.',
            'slug.unique' => 'The generated :attribute conflicts with an existing one. Please change the name.',
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
            'input' => $this->all(),
        ]);

        throw new HttpResponseException(
            redirect()->back()
                ->withErrors($validator)
                ->withInput()
        );
    }
}
