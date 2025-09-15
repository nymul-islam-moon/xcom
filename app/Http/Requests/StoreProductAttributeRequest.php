<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreProductAttributeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Custom attribute names (for prettier errors).
     */
    public function attributes(): array
    {
        return [
            'name'        => 'attribute name',
            'description' => 'attribute description',
            'slug'        => 'attribute slug',
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required'   => 'The :attribute is required.',
            'name.string'     => 'The :attribute must be a valid string.',
            'name.min'        => 'The :attribute must be at least :min characters.',
            'name.unique'     => 'The :attribute has already been taken.',
            'description.string' => 'The :attribute must be a valid string.',
            'slug.unique'     => 'The :attribute has already been taken.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name'        => 'required|string|min:3|unique:product_attributes,name',
            'description' => 'nullable|string',
            'slug'        => 'nullable|string|unique:product_attributes,slug',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('name')) {
            $name = trim($this->input('name'));      // remove spaces
            $name = strtolower($name);               // all lowercase
            $name = ucfirst($name);                  // capitalize first letter
            $this->merge(['name' => $name]);
        }

        // Auto-generate slug if not provided
        if (!$this->filled('slug') && $this->filled('name')) {
            $slug = Str::slug($this->input('name'));
            $this->merge(['slug' => $slug]);
        }
    }
}
