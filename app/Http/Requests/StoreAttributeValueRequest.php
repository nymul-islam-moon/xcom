<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttributeValueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize inputs before validation.
     * - Pull attribute_id from body OR route (supports model binding).
     * - Trim the value.
     */
    protected function prepareForValidation(): void
    {
        $attrFromRoute = $this->route('attribute') ?? $this->route('attribute_id');
        // If route-model binding provides an Attribute model, take its id
        $attributeId = is_object($attrFromRoute) ? ($attrFromRoute->id ?? null) : $attrFromRoute;

        $this->merge([
            'attribute_id' => $this->input('attribute_id', $attributeId),
            'value'        => is_string($this->input('value')) ? trim($this->input('value')) : $this->input('value'),
        ]);
    }

    public function rules(): array
    {
        // after prepareForValidation(), attribute_id is present if available
        $attributeId = $this->input('attribute_id');

        return [
            'attribute_id' => [
                'required',
                'integer',
                Rule::exists('attributes', 'id'),
            ],
            'value' => [
                'required',
                'string',
                'max:255',
               
                Rule::unique('attribute_values', 'value')
                    ->where(fn ($q) => $q->where('attribute_id', $attributeId)),
             
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'attribute_id.required' => 'Please select an attribute.',
            'attribute_id.exists'   => 'The selected attribute does not exist.',
            'value.unique'          => 'This value already exists for the selected attribute.',
        ];
    }

    public function attributes(): array
    {
        return [
            'attribute_id' => 'attribute',
            'value'        => 'attribute value',
        ];
    }
}
