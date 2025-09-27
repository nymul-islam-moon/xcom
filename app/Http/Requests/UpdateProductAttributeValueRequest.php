<?php

namespace App\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\ProductAttributeValue;
use Illuminate\Support\Facades\Log;
use App\Models\ProductAttribute;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;


class UpdateProductAttributeValueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // dd($this->input('product_attribute_id'));

        $value = Str::title(Str::lower(trim($this->input('value'))));

        $attributeSlug = ProductAttribute::where('id', $this->input('product_attribute_id'))->value('slug');


        $slug = implode('-', [
            $attributeSlug,
            Str::slug($value),
        ]);
        $this->merge([
            'value' => $value,
            'slug'  => $slug,
        ]);
    }

    public function rules(): array
    {
        // Grab current record (only available on update, not store)
        $currentAttrVal = $this->route('attributeValue');
        $currentId      = $currentAttrVal?->id;

        // Get attribute_id (from request input or from current record)
        $attributeId = $this->input('product_attribute_id') ?? $currentAttrVal?->product_attribute_id;

        return [
            'product_attribute_id' => [
                'required',
                'exists:product_attributes,id',
            ],

            'value' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_attribute_values', 'value')
                    ->where(fn($q) => $q->where('product_attribute_id', $attributeId))
                    ->ignore($currentId, 'id'), // IMPORTANT: ignore current row
            ],

            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_attribute_values', 'slug')
                    ->where(fn($q) => $q->where('product_attribute_id', $attributeId))
                    ->ignore($currentId, 'id'),
            ],
        ];
    }


    public function messages(): array
    {
        return [
            'value.required' => 'Please provide a value for this attribute.',
            'value.unique'   => 'This value already exists for the selected attribute.',
            'slug.required'  => 'Failed to generate slug for the attribute value.',
        ];
    }

    public function attributes(): array
    {
        return [
            'value' => 'attribute value',
            'slug'  => 'slug',
        ];
    }


    /**
     * Handle failed validation.
     */
    protected function failedValidation(Validator $validator)
    {
        // Log all errors
        Log::error('Product Attribute Value Update validation failed', [
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
