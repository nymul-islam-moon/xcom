<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Models\Brand;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $term = $request->query('q', '');

        $brands = Brand::query()
            ->search($term)
            ->orderBy('name')
            ->paginate(15)
            ->appends(['q' => $term]);

        return view('backend.admin.products.brands.index', compact('brands'));
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

        // slug directly from unique name
        $data['slug'] = Str::slug($data['name']);

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
        return view('admin.products.brands.show', compact('brand'));
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
            Log::error('Brand deletion failed: ' . $e->getMessage(), ['brand_id' => $brand->id]);
            return back()->withErrors(['error' => 'Something went wrong while deleting the brand.']);
        }
    }
}
