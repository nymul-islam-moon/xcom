<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
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
        ];
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
                Rule::unique('product_child_categories', 'name')->ignore($childCategoryId),
            ],
            'product_sub_category_id'   => ['required','integer','exists:product_sub_categories,id'],
            'description'               => ['nullable','string'],
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'name.required'                     => 'Please enter a :attribute.',
            'name.unique'                       => 'The :attribute ":input" is already in use.',
            'name.max'                          => 'The :attribute may not be greater than :max characters.',
            'product_sub_category_id.required'  => 'Please select a :attribute.',
            'product_sub_category_id.exists'    => 'The selected :attribute does not exist.',
        ];
    }
}
