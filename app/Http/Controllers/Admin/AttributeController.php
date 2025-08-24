<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttributeRequest;
use App\Http\Requests\UpdateAttributeRequest;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $term = $request->query('q', '');

        $attributes = Attribute::query()
            ->search($term)
            ->orderBy('name')
            ->paginate(15)
            ->appends(['q' => $term]);

        return view('admin.products.attributes.index', compact('attributes'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.products.attributes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttributeRequest $request)
    {
        DB::beginTransaction();

        try {
            $formData = $request->validated();
            $formData['slug'] = Str::slug($formData['name']);

            Attribute::create($formData);

            DB::commit();

            return redirect()->route('admin.products.attributes.index')
                ->with('success', 'Attribute created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            // Optional: log the actual error
            \Log::error('Attribute creation failed: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong while creating the category.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Attribute $attribute)
    {
        $term = request('q', '');

        $attributeValues = $attribute->values() // relation: Attribute hasMany AttributeValue
            ->when($term !== '', function ($q) use ($term) {
                $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $term) . '%';
                $q->where(function ($w) use ($like) {
                    $w->where('name', 'like', $like)
                        ->orWhere('slug', 'like', $like);
                });
            })
            ->orderBy('name')
            ->paginate(15);

        return view('admin.products.attributes.show', compact('attribute', 'attributeValues'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attribute $attribute)
    {
        return view('admin.products.attributes.edit', compact('attribute'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttributeRequest $request, Attribute $attribute)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attribute $attribute)
    {
        //
    }
}
