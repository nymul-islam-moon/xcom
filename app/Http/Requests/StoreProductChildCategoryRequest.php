<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'name'                      => 'category name',
            'description'               => 'description',
            'product_sub_category_id'   => 'sub-category',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'                      => ['required','string','max:255'],
            'product_sub_category_id'   => ['required','integer','exists:product_sub_categories,id'],
            'description'               => ['nullable','string'],
            // slug is generated in the controller; not accepted from the form
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
        ];
    }

}
