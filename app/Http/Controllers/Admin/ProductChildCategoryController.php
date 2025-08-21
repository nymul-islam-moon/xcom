<?php

namespace App\Http\Controllers\Admin;

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
    public function index(Request $request)
    {
        $term = $request->query('q', '');

        $productChildCategories = ProductChildCategory::query()
            ->with('productSubCategory:id,name')
            ->search($term)
            ->orderby('name')
            ->paginate(15);

        return view('admin.products.childcategories.index', compact('productChildCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.products.childcategories.create');
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
        // return view('admin.products.childcategories.show', compact('childcategory'))
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductChildCategory $child_category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductChildCategoryRequest $request, ProductChildCategory $child_category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductChildCategory $child_category)
    {
        //
    }
}
