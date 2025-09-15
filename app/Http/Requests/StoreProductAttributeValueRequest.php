<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreProductAttributeValueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize inputs before validation:
     * - Pull attribute id from body OR route (supports model binding)
     * - Trim & uppercase the value
     * - Generate a slug (no DB checks here; uniqueness validated in rules())
     */
    protected function prepareForValidation(): void
    {
        $attrFromRoute = $this->route('attribute') ?? $this->route('product_attribute_id');
        $attributeId = is_object($attrFromRoute) ? ($attrFromRoute->id ?? null) : $attrFromRoute;

        $value = Str::ucfirst(Str::lower(trim((string) $this->input('value'))));

        // Base slug from normalized value, fallback to random string if empty.
        $slug = Str::slug((string) $value) ?: Str::random(6);

        $this->merge([
            'product_attribute_id' => $this->input('product_attribute_id', $attributeId),
            'value'                => $value,
            'slug'                 => $slug,
        ]);
    }

    public function rules(): array
    {
        $attributeId = $this->input('product_attribute_id');

        return [
            'product_attribute_id' => [
                'required',
                'integer',
                Rule::exists('product_attributes', 'id'),
            ],
            'value' => [
                'required',
                'string',
                'max:255',
                // Ensure this value is unique *within* the same product attribute
                Rule::unique('product_attribute_values', 'value')
                    ->where(fn($q) => $q->where('product_attribute_id', $attributeId)),
            ],
            // slug is present (prepared) but uniqueness not enforced here because
            // value uniqueness already guarantees unique logical entries.
            'slug' => [
                'required',
                'string',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'product_attribute_id.required' => 'Please select an attribute.',
            'product_attribute_id.exists'   => 'The selected attribute does not exist.',
            'value.required'                => 'Please enter a value for the attribute.',
            'value.unique'                  => 'This value already exists for the selected attribute.',
            'slug.required'                 => 'Failed to generate slug for the attribute value.',
        ];
    }

    public function attributes(): array
    {
        return [
            'product_attribute_id' => 'attribute',
            'value'                => 'attribute value',
            'slug'                 => 'slug',
        ];
    }
}
