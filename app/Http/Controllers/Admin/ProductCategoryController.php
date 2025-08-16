<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductCategoryRequest;
use App\Http\Requests\UpdateProductCategoryRequest;
use App\Models\ProductCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productCategories = ProductCategory::withCount([])->paginate(5);
        return view('admin.products.categories.index', compact('productCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.products.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductCategoryRequest $request)
    {
        DB::beginTransaction();

        try {
            $formData = $request->validated();
            $formData['slug'] = Str::slug($formData['name']);

            ProductCategory::create($formData);

            DB::commit();

            return redirect()->route('admin.products.categories.index')
                ->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            // Optional: log the actual error
            Log::error('Category creation failed: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong while creating the category.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductCategory $productCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductCategory $category)
    {
        return view('admin.products.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductCategoryRequest $request, ProductCategory $category)
    {
        DB::beginTransaction();

        try {
            $formData = $request->validated();
            $formData['slug'] = Str::slug($formData['name']);
            
            $category->update($formData);

            DB::commit();

            return redirect()->route('admin.products.categories.index')
                ->with('success', 'Category updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            // Optional: log the actual error
            Log::error('Category update failed: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the category.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCategory $category)
    {
        DB::beginTransaction();

        try {
            $category->delete();

            DB::commit();

            return redirect()->route('admin.products.categories.index')
                ->with('error', 'Category deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            // Optional: log the actual error
            Log::error('Category deletion failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Something went wrong while deleting the category.');
        }
    }

     /**
     * Get categories for select input.
     */
    public function selectCategories()
    {
        $categories = ProductCategory::select('id', 'name')->get();
        return response()->json($categories);
    }
}
