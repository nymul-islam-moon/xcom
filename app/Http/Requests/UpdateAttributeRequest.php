<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UpdateAttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize inputs before validation.
     * - Support route model binding (`attribute`) or id in URL.
     * - Trim name.
     * - Auto-generate slug from name if not provided.
     */
    protected function prepareForValidation(): void
    {
        $routeAttr = $this->route('attribute') ?? $this->route('attribute_id');
        $attributeId = is_object($routeAttr) ? ($routeAttr->id ?? null) : $routeAttr;

        $name = is_string($this->input('name')) ? trim($this->input('name')) : $this->input('name');
        $slug = $this->input('slug') ?: Str::slug((string) $name);

        $this->merge([
            '_attribute_id' => $attributeId, // internal use for rules()
            'name'          => $name,
            'slug'          => $slug,
        ]);
    }

    public function rules(): array
    {
        $attributeId = $this->input('_attribute_id');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('attributes', 'name')->ignore($attributeId),
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('attributes', 'slug')->ignore($attributeId),
            ],
            'description' => 'nullable|string',
            // If you later add 'description' or other fields, validate them here.
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
}
