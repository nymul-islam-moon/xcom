<?php

namespace App\Http\Controllers\Backend\Admin;

use App\DataTables\Backend\ProductBrandsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Admin\StoreBrandRequest;
use App\Http\Requests\Backend\Admin\UpdateBrandRequest;
use App\Models\Brand;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductBrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ProductBrandsDataTable $dataTable)
    {
        return $dataTable->render('backend.admin.products.brands.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.admin.products.brands.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBrandRequest $request, MediaService $mediaService)
    {
        $data = $request->validated();

        // image (optional)
        if ($path = $mediaService->storeFromRequest($request, 'image', 'brands')) {
            $data['image'] = $path;
        }

        Brand::create($data);

        return redirect()
            ->route('admin.products.brands.index')
            ->with('success', 'Brand created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        return view('backend.admin.products.brands.show', compact('brand'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        return view('backend.admin.products.brands.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBrandRequest $request, Brand $brand, MediaService $mediaService)
    {
        $data = $request->validated();

        // replace image if a new one was uploaded; keep old if not
        if ($path = $mediaService->replaceFromRequest($request, 'image', $brand->image, 'brands')) {
            $data['image'] = $path;
        } else {
            unset($data['image']);
        }

        // always recompute slug from (unique) name
        $data['slug'] = Str::slug($data['name']);

        $brand->update($data);

        return redirect()
            ->route('admin.products.brands.index')
            ->with('success', 'Brand updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand, MediaService $mediaService)
    {
        DB::beginTransaction();
        try {
            // Optional: block deletion if related products exist
            if (method_exists($brand, 'products') && $brand->products()->exists()) {
                return back()->withErrors(['error' => 'Cannot delete this brand because it has products.']);
            }

            // Delete file via service
            $mediaService->deleteFile($brand->image, 'public');

            $brand->delete();

            DB::commit();

            return redirect()
                ->route('admin.products.brands.index')
                ->with('success', 'Brand deleted successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Brand deletion failed: '.$e->getMessage(), ['brand_id' => $brand->id]);

            return back()->withErrors(['error' => 'Something went wrong while deleting the brand.']);
        }
    }

    /**
     * Get brands for select input.
     */
    public function selectBrands(Request $request)
    {
        $q = (string) $request->get('q', '');

        $brands = Brand::select('id', 'name')
            ->where('status', 1)
            ->when(
                $q !== '',
                fn ($query) => $query->where('name', 'like', "%{$q}%")
            )
            ->orderBy('name')
            ->get();

        return response()->json($brands);
    }
}
