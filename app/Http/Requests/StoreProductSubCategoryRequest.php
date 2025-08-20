<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        ];
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:255',
                // unique within the same parent category
                Rule::unique('product_sub_categories', 'name')
                    ->where(fn ($q) => $q->where('product_category_id', $this->input('product_category_id')))
            ],
            'product_category_id' => ['required', 'integer', 'exists:product_categories,id'],
            'description' => ['nullable', 'string'],
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
        ];
    }
}
