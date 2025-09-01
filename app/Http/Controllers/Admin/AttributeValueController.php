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
        return view('admin.products.attributes.values.create', compact('attribute'));
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AttributeValue $attributeValue)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttributeValueRequest $request, AttributeValue $attributeValue)
    {
        //
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
