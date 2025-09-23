<?php

namespace App\Http\Controllers\Backend\Admin;

use App\DataTables\Backend\ProductChildCategoriesDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductChildCategoryRequest;
use App\Http\Requests\UpdateProductChildCategoryRequest;
use App\Models\ProductChildCategory;
use App\Models\ProductSubCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductChildCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ProductChildCategoriesDataTable $dataTable)
    {
        // $term = $request->query('q', '');

        // $productChildCategories = \App\Models\ProductChildCategory::query()
        //     ->with([
        //         // include the FK so Eloquent can hop to productCategory
        //         'productSubCategory:id,name,product_category_id',
        //         // actually load the category too
        //         'productSubCategory.productCategory:id,name',
        //     ])
        //     ->search($term)
        //     ->orderBy('name')
        //     ->paginate(15)
        //     ->appends(['q' => $term]); // keeps search term in pagination links

        return $dataTable->render('backend.admin.products.childcategories.index');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.admin.products.childcategories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductChildCategoryRequest $request)
    {
        DB::beginTransaction();

        try {
            $formData = $request->validated();

            // Build a contextual base slug using parent subcategory (if found)
            $sub = ProductSubCategory::select('id', 'name', 'slug')
                ->find($formData['product_sub_category_id']);

            $base = Str::slug(
                trim(sprintf('%s %s', $sub?->slug ?? $sub?->name ?? '', $formData['name']))
            );

            if ($base === '') {
                $base = Str::slug($formData['name']);
            }

            // Ensure global uniqueness against product_child_categories.slug
            $slug = $base;
            $i = 2;
            while (ProductChildCategory::where('slug', $slug)->exists()) {
                $slug = "{$base}-{$i}";
                $i++;
            }

            // Create record
            ProductChildCategory::create([
                'name' => $formData['name'],
                'slug' => $slug,
                'description' => $formData['description'] ?? null,
                'product_sub_category_id' => $formData['product_sub_category_id'],
            ]);

            DB::commit();

            return redirect()
                ->route('admin.products.child-categories.index')
                ->with('success', 'Child category created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error(
                'Child category create failed: ' . $e->getMessage(),
                ['trace' => $e->getTraceAsString()]
            );

            return back()
                ->withInput()
                ->with('error', 'Failed to create child category.');
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(ProductChildCategory $child_category)
    {
        return view('backend.admin.products.childcategories.show', compact('child_category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductChildCategory $child_category)
    {
        return view('backend.admin.products.childcategories.edit', compact('child_category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductChildCategoryRequest $request, ProductChildCategory $child_category)
    {
        DB::beginTransaction();

        try {
            $formData = $request->validated();

            // Check if we need to regenerate slug
            $regenerateSlug =
                $formData['name'] !== $child_category->name ||
                $formData['product_sub_category_id'] != $child_category->product_sub_category_id;

            $slug = $child_category->slug;

            if ($regenerateSlug) {
                // Get parent subcategory
                $sub = ProductSubCategory::select('id', 'name', 'slug')
                    ->find($formData['product_sub_category_id']);

                $base = Str::slug(
                    trim(sprintf('%s %s', $sub?->slug ?? $sub?->name ?? '', $formData['name']))
                );

                if ($base === '') {
                    $base = Str::slug($formData['name']);
                }

                // Ensure uniqueness (ignore current child_category slug)
                $slug = $base;
                $i = 2;
                while (
                    ProductChildCategory::where('slug', $slug)
                    ->where('id', '!=', $child_category->id)
                    ->exists()
                ) {
                    $slug = "{$base}-{$i}";
                    $i++;
                }
            }

            // Update record
            $child_category->update([
                'name' => $formData['name'],
                'slug' => $slug,
                'description' => $formData['description'] ?? null,
                'product_sub_category_id' => $formData['product_sub_category_id'],
            ]);

            DB::commit();

            return redirect()
                ->route('admin.products.child-categories.index')
                ->with('success', 'Child category updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error(
                'Child category update failed: ' . $e->getMessage(),
                ['trace' => $e->getTraceAsString()]
            );

            return back()
                ->withInput()
                ->with('error', 'Failed to update child category.');
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductChildCategory $child_category)
    {
        DB::beginTransaction();

        try {
            $child_category->delete();

            DB::commit();

            return redirect()
                ->route('admin.products.child-categories.index')
                ->with('error', 'Child category deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Child category deletion failed: ' . $e->getMessage());

            return redirect()
                ->route('admin.products.child-categories.index')
                ->with('error', 'Something went wrong while deleting the child category.');
        }
    }

     /**
     * Get categories for select input.
     */
    public function selectChildCategories(Request $request)
    {
        $q = (string) $request->get('q', '');

        $productChildCategories = ProductChildCategory::select('id', 'name')
            ->where('status', 1)
            ->when(
                $q !== '',
                fn($query) => $query->where('name', 'like', "%{$q}%")
            )
            ->orderBy('name')
            ->get();

        return response()->json($productChildCategories);
    }
}
