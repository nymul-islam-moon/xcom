<?php

namespace App\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UpdateProductCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Stop validation on first failure for this request.
     * Set to true if you prefer "bail" behavior globally here.
     */
    protected $stopOnFirstFailure = true;

     /**
     * Custom attribute names (for prettier errors).
     */
    public function attributes(): array
    {
        return [
            'name'        => 'category name',
            'is_active'   => 'status',
            'description' => 'description',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'name'      => Str::title(Str::lower(trim($this->input('name')))),
            'slug'      => Str::slug(trim($this->input('name'))),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $category = $this->route('category'); // model or id from admin.products.categories.* routes
        $categoryId = is_object($category) ? $category->getKey() : $category;

        return [
            'name'          => ['required', 'string', 'max:255', 'unique:product_categories,name,' . $categoryId],
            'slug'          => ['required', 'string', 'max:255', 'unique:product_categories,slug,' . $categoryId],
            'is_active'     => ['required', 'boolean'],
            'description'   => ['nullable', 'string'],
        ];
    }

     /**
     * Custom messages.
     */
    public function messages(): array
    {
        return [
            'name.required'         => 'Please enter a :attribute.',
            'name.unique'           => 'The :attribute ":input" is already in use.',
            'name.max'              => 'The :attribute may not be greater than :max characters.',
            'is_active.required'    => 'Please select a :attribute.',
            'is_active.boolean'     => 'The :attribute must be true or false.',
        ];
    }


    /**
     * Handle failed validation.
     */
    protected function failedValidation(Validator $validator)
    {
        // Log all errors
        Log::error('Product Category Update validation failed', [
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
