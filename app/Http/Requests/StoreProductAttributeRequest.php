<?php

namespace App\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
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
     * Stop validation on first failure.
     */
    protected $stopOnFirstFailure = true;

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
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'      => Str::title(Str::lower(trim($this->input('name')))),
            'slug'      => Str::slug(trim($this->input('name'))),
        ]);
    }

     /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name'        => 'required|string|min:3|unique:product_attributes,name',
            'slug'        => 'nullable|string|unique:product_attributes,slug',
            'description' => 'nullable|string',
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
     * Handle failed validation.
     */
    protected function failedValidation(Validator $validator)
    {
        // Log all errors
        Log::error('Product Category Store validation failed', [
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
