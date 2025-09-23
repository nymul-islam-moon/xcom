<?php

namespace App\Http\Controllers\Backend\Admin;

use App\DataTables\Backend\ProductSubCategoriesDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductSubCategoryRequest;
use App\Http\Requests\UpdateProductSubCategoryRequest;
use App\Models\ProductSubCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;



class ProductSubCategoryController extends Controller
{

    public function __construct() {}

    /**
     * Display a listing of the resource.
     */
    public function index(ProductSubCategoriesDataTable $dataTable)
    {
        // $term = $request->query('q', '');

        // $productSubCategories = ProductSubCategory::query()
        //     ->withCount(['productChildCategories'])
        //     ->with('productCategory:id,name')
        //     ->search($term)
        //     ->orderBy('name')
        //     ->paginate(15);

        return $dataTable->render('backend.admin.products.subcategories.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view('backend.admin.products.subcategories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductSubCategoryRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
           
            ProductSubCategory::create($data);

            DB::commit();

            return redirect()
                ->route('admin.products.sub-categories.index')
                ->with('success', 'Subcategory created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Subcategory creation failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while creating the subcategory.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductSubCategory $sub_category)
    {
        return view('backend.admin.products.subcategories.show', compact('sub_category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductSubCategory $sub_category)
    {
        return view('backend.admin.products.subcategories.edit', compact('sub_category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductSubCategoryRequest $request, ProductSubCategory $sub_category)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            // Determine target parent (may change on update)
            $targetCategoryId = $data['product_category_id'] ?? $sub_category->product_category_id;

            // Rebuild slug if name changed; otherwise keep current one
            if (array_key_exists('name', $data) && $data['name'] !== $sub_category->name) {
                $data['slug'] = Str::slug($data['name']);
            } else {
                $data['slug'] = $sub_category->slug ?? Str::slug($sub_category->name);
            }

            // Ensure slug uniqueness within the (possibly new) parent category
            $base = $data['slug'];
            $i = 2;
            while (
                ProductSubCategory::where('product_category_id', $targetCategoryId)
                ->where('slug', $data['slug'])
                ->where('id', '!=', $sub_category->id)
                ->exists()
            ) {
                $data['slug'] = "{$base}-{$i}";
                $i++;
            }

            // Ensure FK aligns with target parent
            $data['product_category_id'] = $targetCategoryId;

            $sub_category->update($data);

            DB::commit();

            return redirect()
                ->route('admin.products.sub-categories.index')
                ->with('success', 'Subcategory updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Subcategory update failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the subcategory.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductSubCategory $sub_category)
    {
        DB::beginTransaction();

        try {
            $sub_category->delete();

            DB::commit();

            return redirect()
                ->route('admin.products.sub-categories.index')
                ->with('error', 'Subcategory deleted successfully.');
            // Note: using 'error' key just like your Category destroy method
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Subcategory deletion failed: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Something went wrong while deleting the subcategory.');
        }
    }

    /**
     * Search for subcategories by name or slug.
     */
    public function selectSubCategories(Request $request)
    {
        $q = (string) $request->get('q', '');

        $subCategories = ProductSubCategory::select('id', 'name')
            ->where('status', 1)
            ->when($q !== '', fn($query) => $query->whrere('name', 'like', "%{$q}%"))
            ->orWhere('slug', 'like', "%{$q}%")
            ->orderBy('name')
            ->get();

        return response()->json($subCategories);
    }
}
