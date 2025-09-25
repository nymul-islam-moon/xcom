<?php

namespace App\Http\Requests\Backend\Admin;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use App\Models\ProductSubCategory;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class StoreProductChildCategoryRequest extends FormRequest
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
            'name'                      => 'child category name',
            'description'               => 'description',
            'slug'                      => 'slug',
            'is_active'                 => 'status',
            'product_sub_category_id'   => 'sub-category',
        ];
    }

    protected function prepareForValidation()
    {
        // Normalize name
        $name = Str::title(Str::lower(trim($this->input('name'))));

        $subCategorySlug = ProductSubCategory::where('id', $this->input('product_sub_category_id'))->value('slug');

        $slug = implode('-', [
            $subCategorySlug,
            Str::slug($name),
        ]);

        $this->merge([
            'name'          => $name,
            'slug'          => $slug,
            'is_active'     => $this->boolean('is_active'),
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
            'name' => [
                'required',
                'string',
                'max:255',
                // unique within the same parent sub-category
                Rule::unique('product_child_categories', 'name')
                    ->where(fn($q) => $q->where('product_sub_category_id', $this->input('product_sub_category_id')))
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_child_categories', 'slug'),
            ],
            'product_sub_category_id'   => ['required', 'integer', 'exists:product_sub_categories,id'],
            'is_active'                 => ['required', 'boolean'],
            'description'               => ['nullable', 'string'],
        ];
    }

    /**
     * Custom messages.
     */
    public function messages(): array
    {
        return [
            'name.required'                     => 'Please enter a :attribute.',
            'name.unique'                       => 'The :attribute ":input" is already in use.',
            'name.max'                          => 'The :attribute may not be greater than :max characters.',
            'product_sub_category_id.required'  => 'Please select a :attribute.',
            'product_sub_category_id.exists'    => 'The selected :attribute is already exists.',
            'slug.required'                     => 'The :attribute is required.',
            'slug.unique'                       => 'The generated :attribute conflicts with an existing one. Please change the name.',
            'is_active.required'                => 'Please select a :attribute.',
            'is_active.boolean'                 => 'The selected :attribute is invalid.',
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
