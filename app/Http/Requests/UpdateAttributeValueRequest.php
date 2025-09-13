<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAttributeValueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize inputs before validation.
     * - Pull attribute_id from route (supports model binding).
     * - Trim the value.
     *
     * Note: the blade does not send attribute_id; we rely on route-model binding
     * or a route param named "attribute" / "attribute_id".
     */
    protected function prepareForValidation(): void
    {
        $attrFromRoute = $this->route('attribute') ?? $this->route('attribute_id');
        // If route-model binding provides an Attribute model, take its id
        $attributeId = is_object($attrFromRoute) ? ($attrFromRoute->id ?? null) : $attrFromRoute;

        $this->merge([
            // Merge attribute_id for internal use (won't be validated as required)
            'attribute_id' => $attributeId,
            'value'        => is_string($this->input('value')) ? trim($this->input('value')) : $this->input('value'),
        ]);
    }

    public function rules(): array
    {
        // attribute_id comes from prepareForValidation() (route/model)
        $attributeId = $this->input('attribute_id');

        // Determine current record id from route (model binding or plain id)
        $routeAttrVal = $this->route('attribute_value') ?? $this->route('attribute_value_id');
        $currentId = is_object($routeAttrVal) ? ($routeAttrVal->id ?? null) : $routeAttrVal;

        return [
            'value' => [
                'required',
                'string',
                'max:255',

                // Unique per attribute (attribute_id from route), ignoring current record
                Rule::unique('attribute_values', 'value')
                    ->where(fn ($q) => $q->where('attribute_id', $attributeId))
                    ->ignore($currentId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'value.required' => 'Please provide a value for this attribute.',
            'value.unique'   => 'This value already exists for the selected attribute.',
        ];
    }

    public function attributes(): array
    {
        return [
            'value' => 'attribute value',
        ];
    }
}
