<?php

namespace App\Http\Requests\Backend\Admin;

use App\Models\ProductSubCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UpdateProductChildCategoryRequest extends FormRequest
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
     * Custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'name'                      => 'category name',
            'description'               => 'description',
            'product_sub_category_id'   => 'sub-category',
            'slug'                      => 'slug',
            'is_active'                 => 'status',
        ];
    }

    /**
     * Prepare and normalize inputs before validation.
     * Build a slug that includes the parent category name to avoid collisions across categories.
     */
    protected function prepareForValidation()
    {
        // Normalize name
        $name = Str::title(Str::lower(trim($this->input('name'))));

        // Always build slug from category slug + subcategory name
        $subCategorySlug = ProductSubCategory::where('id', $this->input('product_sub_category_id'))->value('slug');

        $slug = implode('-', [
            $subCategorySlug,
            Str::slug($name),
        ]);

        $this->merge([
            'name'      => $name,
            'slug'      => $slug,
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * Validation rules.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $childCategoryId = $this->route('child_category') ?? $this->route('id');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                // unique within the same parent sub-category, but ignore current record
                Rule::unique('product_child_categories', 'name')
                    ->where(fn($q) => $q->where('product_sub_category_id', $this->input('product_sub_category_id')))
                    ->ignore($childCategoryId),
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_child_categories', 'slug')->ignore($childCategoryId),
            ],
            'product_sub_category_id'   => ['required', 'integer', 'exists:product_sub_categories,id'],
            'is_active'                 => ['required', 'boolean'],
            'description'               => ['nullable', 'string'],
        ];
    }


    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'name.required'                     => 'Please enter a :attribute.',
            'name.unique'                       => 'The :attribute ":input" is already in use within the selected sub-category.',
            'name.max'                          => 'The :attribute may not be greater than :max characters.',

            'slug.required'                     => 'Please enter a :attribute.',
            'slug.unique'                       => 'The :attribute ":input" is already in use.',
            'slug.max'                          => 'The :attribute may not be greater than :max characters.',

            'product_sub_category_id.required'  => 'Please select a :attribute.',
            'product_sub_category_id.exists'    => 'The selected :attribute does not exist.',

            'is_active.required'                => 'Please select a :attribute.',
            'is_active.boolean'                 => 'The :attribute must be true or false.',

            'description.string'                => 'The :attribute must be a valid string.',
        ];
    }
}
