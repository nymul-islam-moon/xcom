<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttributeValueRequest;
use App\Http\Requests\UpdateAttributeValueRequest;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AttributeValueController extends Controller
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
        $attribute = Attribute::findOrFail($attributeId);
        // dd($attribute);
        return view('backend.admin.products.attributes.values.create', compact('attribute'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttributeValueRequest $request)
    {
        DB::beginTransaction();

        try {
            // validated data guaranteed to contain 'value' per your validation rules
            $data = $request->validated();

            // normalize value: trim and uppercase (one-liner)
            $data['value'] = strtoupper(trim((string) $data['value']));

            // base slug (fall back to random string if slug is empty)
            $base = Str::slug($data['value']) ?: Str::random(6);

            // ensure uniqueness for (attribute_id, slug)
            $slug = $base;
            $n = 2;
            while (AttributeValue::where('attribute_id', $data['attribute_id'])
                ->where('slug', $slug)
                ->exists()
            ) {
                $slug = "{$base}-{$n}";
                $n++;
            }
            $data['slug'] = $slug;

            // create and commit
            $attributeValue = AttributeValue::create($data);

            DB::commit();

            return redirect()
                ->route('admin.products.attributes.show', $attributeValue->attribute_id)
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
    public function show(AttributeValue $attributeValue)
    {
        return view('backend.admin.products.attributes.values.show', compact('attributeValue'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AttributeValue $attributeValue)
    {
        return view('backend.admin.products.attributes.values.edit', compact('attributeValue'));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(UpdateAttributeValueRequest $request, AttributeValue $attributeValue)
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
    public function destroy(AttributeValue $attributeValue)
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
