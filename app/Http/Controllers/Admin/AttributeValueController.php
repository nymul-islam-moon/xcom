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
            $formData = $request->validated();
            // 1) Base slug from the provided value
            $base = Str::slug((string)($formData['value'] ?? ''));

            // Fallback to something non-empty just in case
            if ($base === '') {
                $base = Str::random(6);
            }

            // 2) Ensure uniqueness for (attribute_id, slug)
            $slug = $base;
            $n = 2;
            while (
                AttributeValue::where('attribute_id', $formData['attribute_id'])
                ->where('slug', $slug)
                ->exists()
            ) {
                $slug = "{$base}-{$n}";
                $n++;
            }

            $formData['slug'] = $slug;

            // dd($formData);

            // 3) Create
            $attributeValue = AttributeValue::create($formData);

            DB::commit();

            return redirect()
                ->route('admin.products.attributes.show', $attributeValue->attribute_id)
                ->with('success', 'Attribute value created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Attribute Value creation failed: ' . $e->getMessage());
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
            $formData = $request->validated();

            // Normalize: always uppercase the first character of the value
            if (isset($formData['value']) && is_string($formData['value'])) {
                $formData['value'] = ucfirst(strtolower(trim($formData['value'])));
            }

            // final attribute_id (shouldn't change, but fallback just in case)
            $finalAttributeId = $formData['attribute_id'] ?? $attributeValue->attribute_id;

            // CASE A: value changed → regenerate slug
            if (array_key_exists('value', $formData) && $formData['value'] !== $attributeValue->value) {
                $base = Str::slug((string) $formData['value']);

                if ($base === '') {
                    $base = Str::random(6);
                }

                $slug = $base;
                $n = 2;
                while (
                    AttributeValue::where('attribute_id', $finalAttributeId)
                    ->where('slug', $slug)
                    ->where('id', '!=', $attributeValue->id)
                    ->exists()
                ) {
                    $slug = "{$base}-{$n}";
                    $n++;
                }

                $formData['slug'] = $slug;
            }
            // CASE B: value unchanged but attribute_id changed → ensure slug uniqueness
            elseif (isset($formData['attribute_id']) && $formData['attribute_id'] != $attributeValue->attribute_id) {
                $existingSlug = $attributeValue->slug
                    ?: Str::slug((string) ($attributeValue->value ?? ''))
                    ?: Str::random(6);

                $slug = $existingSlug;
                $n = 2;
                while (
                    AttributeValue::where('attribute_id', $finalAttributeId)
                    ->where('slug', $slug)
                    ->where('id', '!=', $attributeValue->id)
                    ->exists()
                ) {
                    $slug = "{$existingSlug}-{$n}";
                    $n++;
                }
                $formData['slug'] = $slug;
            }
            // else → no changes to value or attribute_id → keep slug as-is

            // Update model
            $attributeValue->update($formData);

            DB::commit();

            $redirectAttributeId = $formData['attribute_id'] ?? $attributeValue->attribute_id;

            return redirect()
                ->route('admin.products.attributes.show', $redirectAttributeId)
                ->with('success', 'Attribute value updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Attribute Value Update Failed: ' . $e->getMessage());

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
