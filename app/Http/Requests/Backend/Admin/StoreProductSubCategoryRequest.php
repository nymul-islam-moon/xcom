<?php

namespace App\Http\Requests\Backend\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Models\ProductCategory;

class StoreProductSubCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected $stopOnFirstFailure = true;

    public function attributes(): array
    {
        return [
            'name'                  => 'subcategory name',
            'description'           => 'description',
            'product_category_id'   => 'category',
            'slug'                  => 'slug',
            'is_active'             => 'status'
        ];
    }

    protected function prepareForValidation()
    {
        // Normalize name
        $name = Str::title(Str::lower(trim($this->input('name'))));

        // Always build slug from category slug + subcategory name
        $categorySlug = ProductCategory::where('id', $this->input('product_category_id'))->value('slug');

        $slug = implode('-', [
            $categorySlug,
            Str::slug($name),
        ]);

        $this->merge([
            'name'      => $name,
            'slug'      => $slug,
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                // unique within the same parent category
                Rule::unique('product_sub_categories', 'name')
                    ->where(fn($q) => $q->where('product_category_id', $this->input('product_category_id')))
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_sub_categories', 'slug'),
            ],
            'product_category_id'   => ['required', 'integer', 'exists:product_categories,id'],
            'is_active'             => ['required', 'boolean'],
            'description'           => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'                     => 'Please enter a :attribute.',
            'name.unique'                       => 'The :attribute ":input" already exists in the selected category.',
            'name.max'                          => 'The :attribute may not be greater than :max characters.',
            'product_category_id.required'      => 'Please select a :attribute.',
            'product_category_id.exists'        => 'The selected :attribute is invalid.',
            'slug.required'                     => 'The :attribute is required.',
            'slug.unique'                       => 'The generated :attribute conflicts with an existing one. Please change the name.',
            'is_active.required'                => 'Please select a :attribute.',
            'is_active.boolean'                 => 'The selected :attribute is invalid.',
        ];
    }
}
