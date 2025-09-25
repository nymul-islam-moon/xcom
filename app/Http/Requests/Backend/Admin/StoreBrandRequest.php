<?php

namespace App\Http\Requests\Backend\Admin;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;


class StoreBrandRequest extends FormRequest
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
            'name'          => 'category name',
            'image'         => 'image',
            'is_active'     => 'status',
            'description'   => 'description',
        ];
    }

    /**
     * Prepare data before validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'name'      => Str::title(Str::lower(trim($this->input('name')))),
            'slug'      => Str::slug(trim($this->input('name'))),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:255|unique:brands,name',
            'slug'          => ['required', 'string', 'max:255', 'unique:brands,slug'],
            'is_active'     => 'required|boolean',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description'   => 'nullable|string|max:1000',
        ];
    }

    /**
     * Custom messages.
     */
    public function messages(): array
    {
        return [
            'name.required'     => 'Please enter a :attribute.',
            'name.string'       => 'The :attribute must be a valid string.',
            'name.max'          => 'The :attribute may not be greater than :max characters.',
            'name.unique'       => 'The :attribute ":input" is already in use.',

            'is_active.required' => 'Please select a :attribute.',
            'is_active.boolean' => 'The :attribute must be true or false.',

            'image.image'       => 'The :attribute must be an image file.',
            'image.mimes'       => 'The :attribute must be a file of type: :values.',
            'image.max'         => 'The :attribute may not be greater than :max kilobytes.',

            'description.string' => 'The :attribute must be a valid string.',
            'description.max'   => 'The :attribute may not be greater than :max characters.',
        ];
    }

    /**
     * Handle failed validation.
     */
    protected function failedValidation(Validator $validator)
    {
        // Log all errors
        Log::error('Product Brand Store validation failed', [
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
