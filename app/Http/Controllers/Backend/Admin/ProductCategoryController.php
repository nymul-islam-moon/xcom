<?php

namespace App\Http\Controllers\Backend\Admin;

use App\DataTables\Backend\ProductCategoriesDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductCategoryRequest;
use App\Http\Requests\UpdateProductCategoryRequest;
use App\Models\ProductCategory;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductCategoryController extends Controller
{

    public function __construct() {}

    /**
     * Display a listing of the resource.
     */
    public function index(ProductCategoriesDataTable $dataTable)
    {

        return $dataTable->render('backend.admin.products.categories.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.admin.products.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductCategoryRequest $request)
    {
        DB::beginTransaction();

        try {
            $formData = $request->validated();

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
    public function show(ProductCategory $category)
    {
        return view('backend.admin.products.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductCategory $category)
    {
        return view('backend.admin.products.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductCategoryRequest $request, ProductCategory $category)
    {
        DB::beginTransaction();

        try {
            $formData = $request->validated();
            
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
    public function selectCategories(Request $request)
    {
        $q = (string) $request->get('q', '');

        $categories = ProductCategory::select('id', 'name')
            ->where('status', 1)
            ->when(
                $q !== '',
                fn($query) => $query->where('name', 'like', "%{$q}%")
            )
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }
}
