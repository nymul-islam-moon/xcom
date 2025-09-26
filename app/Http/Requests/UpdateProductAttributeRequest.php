<?php

namespace App\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UpdateProductAttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected $stopOnFirstFailure = true;
    
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'      => Str::title(Str::lower(trim($this->input('name')))),
            'slug'      => Str::slug(trim($this->input('name'))),
        ]);
    }

    public function rules(): array
    {
        $attribute = $this->route('attribute');
        $attributeId = is_object($attribute) ? $attribute->getKey() : $attribute;

        return [
            'name'          => ['required', 'string', 'max:255', 'unique:product_attributes,name,' . $attributeId],
            'slug'          => ['required', 'string', 'max:255', 'unique:product_attributes,slug,' . $attributeId],
            'description'   => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please provide an attribute name.',
            'name.unique'   => 'An attribute with this name already exists.',
            'slug.required' => 'A slug is required (it is generated from the name if omitted).',
            'slug.unique'   => 'An attribute with this slug already exists.',
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
