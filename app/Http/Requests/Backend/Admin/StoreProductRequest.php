<?php

namespace App\Http\Requests\Backend\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;



class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Adjust this to your authorization logic (e.g. check user role/permissions).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * This is tuned to the fields present in your create blade.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Common enums
        $productTypes = ['physical', 'digital', 'subscription', 'service', 'gift_card'];
        $variantTypes = ['simple', 'variable'];
        $statuses = ['active', 'inactive', 'out_of_stock', 'discontinued'];
        $backorderOpts = ['no', 'notify', 'yes'];

        return [

            'shop_id'               => ['required', 'integer', 'exists:shops,id'],

            // Basic info
            'name'                  => ['required', 'string', 'max:255'],

            // Types & variants
            'product_type'          => ['required', Rule::in($productTypes)],
            'variant_type'          => ['required', Rule::in($variantTypes)],

            'sku' => [
                'nullable',
                Rule::requiredIf($this->input('variant_type') === 'simple'),
                'string',
                'max:100',
                Rule::unique('products', 'sku'),
            ],
            // slug should be unique on store; if you use this request for update adjust accordingly
            'slug'                  => ['required', 'string', 'max:255', 'alpha_dash', 'unique:products,slug'],
            'short_description'     => ['nullable', 'string', 'max:1000'],
            'description'           => ['nullable', 'string'],

            // Category & Brand (nullable but must exist when present)
            'category_id'           => ['required', 'integer', 'exists:product_categories,id'],
            'subcategory_id'        => ['nullable', 'integer', 'exists:product_sub_categories,id'],
            'child_category_id'     => ['nullable', 'integer', 'exists:product_child_categories,id'],
            'brand_id'              => ['nullable', 'integer', 'exists:brands,id'],

            // Flags / enums
            'status'                => ['required', Rule::in($statuses)],
            'is_featured'           => ['sometimes', 'boolean'],
            'tax_included'          => ['sometimes', 'boolean'],
            'tax_percentage'        => ['nullable', 'numeric', 'between:0,100'],

            'allow_backorders'      => ['nullable', Rule::in($backorderOpts)],
            'restock_date'          => ['nullable', 'date'],

            // Identifiers
            'mpn'                   => ['nullable', 'string', 'max:100'],
            'gtin8'                 => ['nullable', 'string', 'max:20'],
            'gtin13'                => ['nullable', 'string', 'max:20'],
            'gtin14'                => ['nullable', 'string', 'max:20'],

            // Return / publish
            'return_policy'         => ['nullable', 'string'],
            'return_days'           => ['nullable', 'integer', 'min:0'],

            'publish_date'          => ['nullable', 'date'],
            'is_published'          => ['sometimes', 'boolean'],

            // Digital / subscription specifics
            'download_url' => ['nullable', 'url', 'required_if:product_type,digital'],
            'license_key'           => ['nullable', 'string', 'max:100'],
            'subscription_interval' => ['nullable', 'string', 'max:50'],

            // Simple pricing/stock — required when product is simple variant OR non-variable product
            'price'                 => ['nullable', 'numeric', 'min:0', 'required_if: variant_type,simple'],
            'sale_price'            => ['nullable', 'numeric', 'min:0', 'lte:price'],
            'stock_quantity'        => ['nullable', 'integer', 'min:0'],

            'weight'                => ['nullable', 'numeric', 'min:0'],
            'width'                 => ['nullable', 'numeric', 'min:0'],
            'height'                => ['nullable', 'numeric', 'min:0'],
            'depth'                 => ['nullable', 'numeric', 'min:0'],
            'low_stock_threshold'   => ['nullable', 'integer', 'min:0'],

            // Images
            'main_image'            => ['nullable', 'image', 'max:5120'], // max 5MB
            'gallery_images'        => ['nullable', 'array'],
            'gallery_images.*'      => ['image', 'max:5120'],

            //
            // Attributes (server renders attribute selects). Each attribute select is an array of value ids.
            // Example input: attribute_values[1] => [2,3]
            //
            'attributes'                      => ['nullable', 'array'],
            'attributes.*'                    => ['nullable', 'array'],
            'attributes.*.*'                  => ['integer', 'distinct', 'exists:product_attribute_values,id'],

            //
            // Combinations (for variable products)
            // Expect: combinations => [
            //   0 => ['price'=>..., 'sale_price'=>..., 'stock_quantity'=>..., 'sku'=>..., 'attributes'=>[valId1,valId2], 'is_default'=>0|1]
            // ]
            //
            'combinations'                          => ['nullable', 'array'],
            'combinations.*.price'                  => ['nullable', 'numeric', 'min:0'],
            'combinations.*.sale_price'             => ['nullable', 'numeric', 'min:0'],
            'combinations.*.stock_quantity'         => ['nullable', 'integer', 'min:0'],
            'combinations.*.sku'                    => ['nullable', 'string', 'max:100'],
            'combinations.*.slug'                   => ['nullable', 'string', 'max:100'],
            'combinations.*.attribute_values'       => ['required_with:combinations.*', 'array', 'min:1'],
            'combinations.*.attribute_values.*'     => ['integer', 'exists:product_attribute_values,id'],
            'combinations.*.is_default'             => ['sometimes', 'in:0,1'], // you send hidden 0 and checkbox 1
            // Per-combination uploaded files (optional)
            'combinations.*.main_image'             => ['nullable', 'file', 'image', 'max:5120'],
            'combinations.*.gallery_images'         => ['nullable', 'array'],
            'combinations.*.gallery_images.*'       => ['file', 'image', 'max:5120'],
        ];
    }


    public function messages()
    {
        return [];
    }

    /**
     * Modify input prior to validation if necessary.
     *
     * Example: ensure checkboxes/hiddens are normalized to int/bool.
     */
    protected function prepareForValidation(): void
    {
        // Normalize boolean-like fields
        $this->merge([
            'name'          => Str::title(Str::lower(trim($this->input('name')))),
            'is_featured'   => $this->boolean('is_featured'),
            'tax_included'  => $this->boolean('tax_included'),
            'is_published'  => $this->boolean('is_published'),
            'shop_id'       => auth()->guard('shop')->id(),
        ]);

        // Normalize combinations is_default values (strings '0'|'1' -> int)
        $combinations = $this->input('combinations', []);
        if (is_array($combinations)) {
            foreach ($combinations as $i => $comb) {
                if (isset($comb['is_default'])) {
                    $combinations[$i]['is_default'] = (int) $comb['is_default'];
                }
            }
            $this->merge(['combinations' => $combinations]);
        }
    }


    protected function failedValidation(Validator $validator)
    {
        // Log all errors
        Log::error('Product validation failed', [
            'errors' => $validator->errors()->toArray(),
            'input'  => $this->all(),
        ]);

        // Optional: throw exception so the usual redirect with errors happens
        throw new HttpResponseException(
            redirect()->back()
                ->withErrors($validator)
                ->withInput()
        );
    }
}
