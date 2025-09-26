<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductAttributeValueRequest;
use App\Http\Requests\UpdateProductAttributeValueRequest;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductAttributeValueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($attributeId)
    {
        $attribute = ProductAttribute::findOrFail($attributeId);
        // dd($attribute);
        return view('backend.admin.products.attributes.values.create', compact('attribute'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductAttributeValueRequest $request)
    {
        DB::beginTransaction();

        try {
            // validated data guaranteed to contain 'value' per your validation rules
            $data = $request->validated();
          
            $attributeValue = ProductAttributeValue::create($data);

            DB::commit();

            return redirect()
                ->route('admin.products.attributes.show', $attributeValue->product_attribute_id)
                ->with('success', 'Attribute value created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Attribute Value creation failed: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors(['error' => 'Failed to create attribute value: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductAttributeValue $attributeValue)
    {
        return view('backend.admin.products.attributes.values.show', compact('attributeValue'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductAttributeValue $attributeValue)
    {
        return view('backend.admin.products.attributes.values.edit', compact('attributeValue'));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(UpdateProductAttributeValueRequest $request, ProductAttributeValue $attributeValue)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            // normalize value: uppercase first character
            if (isset($data['value']) && is_string($data['value'])) {
                $data['value'] = strtoupper(strtolower(trim($data['value'])));
            }

            // regenerate slug if value changed
            if (array_key_exists('value', $data) && $data['value'] !== $attributeValue->value) {
                $data['slug'] = Str::slug($data['value']) ?: Str::random(6);
            }

            // perform update
            $attributeValue->update($data);

            DB::commit();

            return redirect()
                ->route('admin.products.attributes.show', $data['attribute_id'] ?? $attributeValue->attribute_id)
                ->with('success', 'Attribute value updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Attribute Value Update Failed: ' . $e->getMessage(), ['exception' => $e]);

            return back()->withErrors(['error' => 'Failed to update attribute value: ' . $e->getMessage()]);
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductAttributeValue $attributeValue)
    {
        DB::beginTransaction();

        try {
            $attributeValue->delete();
            DB::commit();

            return redirect()->route('admin.products.attributes.show', $attributeValue->attribute_id);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Attribute Value Deleting Failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Something went wrong while deleting the attribute value.');
        }
    }
}
