<?php

namespace App\Http\Controllers\Backend\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Models\ProductAttributeValue;
use App\Services\MediaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::latest()->paginate(10); // instead of get()

        return view('backend.shop.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.shop.products.create', [
            'attributes' => ProductAttribute::with('values')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request, MediaService $mediaService)
    {
        DB::beginTransaction();

        try {
            $formData = $request->validated();
            // dd($formData);
            // Create product
            $product = Product::create([
                'name' => $formData['name'],
                'sku' => $formData['sku'] ?? null,
                'slug' => $formData['slug'] ?? null,
                'short_description' => $formData['short_description'] ?? null,
                'description' => $formData['description'] ?? null,
                'product_type' => $formData['product_type'],
                'variant_type' => $formData['variant_type'],
                'category_id' => $formData['category_id'] ?? null,
                'subcategory_id' => $formData['subcategory_id'] ?? null,
                'child_category_id' => $formData['child_category_id'] ?? null,
                'brand_id' => $formData['brand_id'] ?? null,
                'status' => $formData['status'],
                'is_featured' => $formData['is_featured'] ?? false,
                'tax_included' => $formData['tax_included'] ?? false,
                'tax_percentage' => $formData['tax_percentage'] ?? null,
                'allow_backorders' => $formData['allow_backorders'] ?? null,
                'restock_date' => $formData['restock_date'] ?? null,
                'mpn' => $formData['mpn'] ?? null,
                'gtin8' => $formData['gtin8'] ?? null,
                'gtin13' => $formData['gtin13'] ?? null,
                'gtin14' => $formData['gtin14'] ?? null,
                'return_policy' => $formData['return_policy'] ?? null,
                'return_days' => $formData['return_days'] ?? null,
                'publish_date' => $formData['publish_date'] ?? null,
                'is_published' => $formData['is_published'] ?? false,
                'download_url' => $formData['download_url'] ?? null,
                'license_key' => $formData['license_key'] ?? null,
                'subscription_interval' => $formData['subscription_interval'] ?? null,
            ]);

            // Product main image
            if ($request->hasFile('main_image')) {
                $path = $mediaService->storeFile($request->file('main_image'), 'products/main', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_main' => true,
                ]);
            }

            // Product gallery images
            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $file) {
                    $path = $mediaService->storeFile($file, 'products/gallery', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                    ]);
                }
            }

            // Simple product variant
            if ($formData['variant_type'] === 'simple') {
                $product->variants()->create([
                    'sku'                   => $formData['sku'] ?? null,
                    'slug'                  => $formData['slug'] ?? null,
                    'price'                 => $formData['price'] ?? 0,
                    'sale_price'            => $formData['sale_price'] ?? null,
                    'stock_quantity'        => $formData['stock_quantity'] ?? 0,
                    'weight'                => $formData['weight'] ?? null,
                    'width'                 => $formData['width'] ?? null,
                    'height'                => $formData['height'] ?? null,
                    'depth'                 => $formData['depth'] ?? null,
                    'low_stock_threshold'   => $formData['low_stock_threshold'] ?? null,
                    'is_default'            => true,
                ]);
            }

            // Variable product variants
            if ($formData['variant_type'] === 'variable' && !empty($formData['combinations'])) {
                foreach ($formData['combinations'] as $comb) {
                    $variant = $product->variants()->create([
                        'sku'                   => $comb['sku'] ?? null,
                        'slug'                  => $comb['slug'] ?? null,
                        'price'                 => $comb['price'] ?? null,
                        'sale_price'            => $comb['sale_price'] ?? null,
                        'stock_quantity'        => $comb['stock_quantity'] ?? null,
                        'weight'                => $comb['weight'] ?? null,
                        'width'                 => $comb['width'] ?? null,
                        'height'                => $comb['height'] ?? null,
                        'depth'                 => $comb['depth'] ?? null,
                        'low_stock_threshold'   => $comb['low_stock_threshold'] ?? null,
                        'is_default'            => $comb['is_default'] ?? false,
                    ]);

                    // Attach attributes to pivot
                    if (!empty($comb['attributes'])) {
                        $attachPayload = [];
                        foreach ($comb['attributes'] as $attr) {
                            if (is_array($attr)) {
                                $valueId = $attr['product_attribute_value_id'] ?? null;
                                $attributeId = $attr['product_attribute_id'] ?? null;
                            } else {
                                $valueId = (int) $attr;
                                $valueModel = ProductAttributeValue::select('id', 'product_attribute_id')->find($valueId);
                                $attributeId = $valueModel ? $valueModel->product_attribute_id : null;
                            }
                            if ($valueId) $attachPayload[$valueId] = ['product_attribute_id' => $attributeId];
                        }
                        if (!empty($attachPayload)) $variant->attributes()->sync($attachPayload);
                    }

                    // Variant main image
                    if (!empty($comb['main_image']) && $comb['main_image'] instanceof \Illuminate\Http\UploadedFile) {
                        $variantPath = $mediaService->storeFile($comb['main_image'], 'products/variants', 'public');
                        $variant->update(['main_image' => $variantPath]);
                    }

                    // Variant gallery images
                    if (!empty($comb['gallery_images'])) {
                        foreach ($comb['gallery_images'] as $gfile) {
                            if ($gfile instanceof \Illuminate\Http\UploadedFile) {
                                $galleryPath = $mediaService->storeFile($gfile, 'products/variants', 'public');
                                $variant->images()->create(['image_path' => $galleryPath]);
                            }
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('shop.products.index')
                ->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating product: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'There was an error creating the product. Please try again.']);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product, MediaService $mediaService)
    {
        DB::beginTransaction();

        try {
            // Delete product images
            foreach ($product->images as $image) {
                $mediaService->deleteFile($image->image_path);
                $image->delete();
            }

            // Delete variants and their images & pivot attributes
            foreach ($product->variants as $variant) {
                // Delete variant gallery images
                foreach ($variant->images as $vImage) {
                    $mediaService->deleteFile($vImage->path);
                    $vImage->delete();
                }

                // Delete main image if exists
                $mediaService->deleteFile($variant->main_image);

                // Delete pivot attributes
                $variant->attributes()->detach();

                $variant->delete();
            }

            // Delete the product itself
            $product->delete();

            DB::commit();

            return redirect()->route('shop.products.index')
                ->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting product: ' . $e->getMessage());

            return redirect()->back()
                ->withErrors(['error' => 'There was an error deleting the product. Please try again.']);
        }
    }
}
