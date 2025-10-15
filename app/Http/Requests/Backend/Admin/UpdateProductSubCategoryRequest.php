<?php

namespace App\Http\Requests\Backend\Admin;

use App\Models\ProductCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateProductSubCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected $stopOnFirstFailure = true;

    public function attributes(): array
    {
        return [
            'name' => 'subcategory name',
            'description' => 'description',
            'product_category_id' => 'category',
            'slug' => 'slug',
            'is_active' => 'status',
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
        $categorySlug = ProductCategory::where('id', $this->input('product_category_id'))->value('slug');

        $slug = implode('-', [
            $categorySlug,
            Str::slug($name),
        ]);

        $this->merge([
            'name' => $name,
            'slug' => $slug,
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        $sub = $this->route('sub_category');

        $slugUniqueRule = Rule::unique('product_sub_categories', 'slug');
        if ($sub?->id) {
            $slugUniqueRule = $slugUniqueRule->ignore($sub->id);
        }

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                // UNIQUE within the same parent category, but ignore this row
                Rule::unique('product_sub_categories', 'name')
                    ->where(fn ($q) => $q->where(
                        'product_category_id',
                        $this->input('product_category_id', $sub?->product_category_id)
                    ))
                    ->ignore($sub?->id),
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                $slugUniqueRule,
            ],
            'product_category_id' => ['required', 'integer', 'exists:product_categories,id'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please enter a :attribute.',
            'name.unique' => 'The :attribute ":input" already exists in the selected category.',
            'name.max' => 'The :attribute may not be greater than :max characters.',
            'product_category_id.required' => 'Please select a :attribute.',
            'product_category_id.exists' => 'The selected :attribute is invalid.',
            'slug.required' => 'The :attribute is required.',
            'slug.unique' => 'The generated :attribute conflicts with an existing one. Please change the name or category.',
            'is_active.required' => 'Please select a :attribute.',
            'is_active.boolean' => 'The selected :attribute is invalid.',
        ];
    }
}
