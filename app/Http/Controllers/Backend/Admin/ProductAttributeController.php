<?php

namespace App\Http\Controllers\Backend\Admin;

use App\DataTables\Backend\ProductAttributesDataTable;
use App\DataTables\Backend\ProductAttributeValuesDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductAttributeRequest;
use App\Http\Requests\UpdateProductAttributeRequest;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductAttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ProductAttributesDataTable $dataTable)
    {
        return $dataTable->render('backend.admin.products.attributes.index');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.admin.products.attributes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductAttributeRequest $request)
    {
        DB::beginTransaction();

        try {
            $formData = $request->validated();
            
            ProductAttribute::create($formData);

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
    public function show(ProductAttribute $attribute, ProductAttributeValuesDataTable $dataTable)
    {

        return $dataTable->render('backend.admin.products.attributes.show', compact('attribute'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductAttribute $attribute)
    {
        return view('backend.admin.products.attributes.edit', compact('attribute'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductAttributeRequest $request, ProductAttribute $attribute)
    {
        DB::beginTransaction();

        try {
            $formData = $request->validated();

            $attribute->update($formData);

            DB::commit();

            return redirect()
                ->route('admin.products.attributes.index', $attribute)
                ->with('success', 'Attribute updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Attribute update failed: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the attribute.');
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductAttribute $attribute)
    {
        DB::beginTransaction();

        try {
            $attribute->delete();

            DB::commit();

            return redirect()->route('admin.products.attributes.index')->with('error', 'Attribute delete successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Attribute deleting failed: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Something went wrong while deleting attribute');
        }
    }
}
