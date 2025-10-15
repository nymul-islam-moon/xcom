<?php

namespace App\Http\Requests;

use App\Models\ProductAttribute;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreProductAttributeValueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected $stopOnFirstFailure = true;

    public function attributes(): array
    {
        return [
            'value' => 'attribute value',
            'slug' => 'slug',
            'product_attribute_id' => 'attribute',
        ];
    }

    protected function prepareForValidation(): void
    {
        $value = Str::title(Str::lower(trim($this->input('value'))));

        $attributeSlug = ProductAttribute::where('id', $this->input('product_attribute_id'))->value('slug');

        $slug = implode('-', [
            $attributeSlug,
            Str::slug($value),
        ]);

        $this->merge([
            'value' => $value,
            'slug' => $slug,
        ]);
    }

    public function rules(): array
    {
        $attributeId = $this->input('product_attribute_id');

        return [

            'value' => [
                'required',
                'string',
                'max:255',
                // Ensure this value is unique *within* the same product attribute
                Rule::unique('product_attribute_values', 'value')
                    ->where(fn ($q) => $q->where('product_attribute_id', $attributeId)),
            ],

            'product_attribute_id' => [
                'required',
                'integer',
                Rule::exists('product_attributes', 'id'),
            ],

            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_attribute_values', 'slug'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'product_attribute_id.required' => 'Please select an attribute.',
            'product_attribute_id.exists' => 'The selected attribute does not exist.',
            'value.required' => 'Please enter a value for the attribute.',
            'value.unique' => 'This value already exists for the selected attribute.',
            'slug.required' => 'Failed to generate slug for the attribute value.',
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
