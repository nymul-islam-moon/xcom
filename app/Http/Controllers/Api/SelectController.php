<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Http\Request;

class SelectController extends Controller
{
    public function ActiveInactiveSelect(Request $request)
    {
        // Simple static list. Use ids as strings or ints consistently.
        $options = [
            ['id' => '1', 'text' => 'Active'],
            ['id' => '0', 'text' => 'Inactive'],
        ];

        // Optional: if search term provided, filter (useful if you later expand)
        if ($request->filled('q')) {
            $q = strtolower($request->q);
            $options = array_filter($options, function ($opt) use ($q) {
                return strpos(strtolower($opt['text']), $q) !== false;
            });
            // Re-index array
            $options = array_values($options);
        }

        return response()->json($options);
    }

    public function brandSelect(Request $request)
    {
        $q = Brand::where('is_active', 1);

        if ($request->filled('q')) {
            $q->where('name', 'like', '%' . $request->q, '%');
        }

        $brands = $q->select('id', 'name')->orderBy('name')->get();

        return response()->json($brands);
    }

    public function categorySelect(Request $request)
    {
        // Support Select2: q param (search), page param for pagination
        $q = ProductCategory::where('is_active', 1);

        if ($request->filled('q')) {
            $q->where('name', 'like', '%' . $request->q . '%');
        }

        // Otherwise return simple array [{id,name}, ...]
        $cats = $q->select('id', 'name')->orderBy('name')->get();
        return response()->json($cats);
    }

    public function subCategorySelect(Request $request, int $categoryId)
    {
        try {
            // Find category
            $category = ProductCategory::find($categoryId);

            if (! $category) {
                return response()->json([]);
            }

            // Build query from relation
            $q = $category->productSubCategories()->where('is_active', 1);

            // Apply search if present
            if ($request->filled('q')) {
                $q->where('name', 'like', '%' . $request->q . '%');
            }

            // Always return simple array [{id, name}, ...]
            $subs = $q->select('id', 'name')->orderBy('name')->get();

            return response()->json($subs);
        } catch (\Throwable $e) {
            Log::error('Failed to fetch subcategories for category id ' . $categoryId, [
                'exception' => $e,
                'category_id' => $categoryId,
                'request_q' => $request->q ?? null,
            ]);

            return response()->json([
                'message' => 'Something went wrong while fetching subcategories.'
            ], 500);
        }
    }

    public function childCategorySelect(Request $request, int $subCategoryId)
    {
        try {
            // Find category
            $subCategory = ProductSubCategory::find($subCategoryId);

            if (! $subCategory) {
                return response()->json([]);
            }

            // Build query from relation
            $q = $subCategory->productChildCategories()->where('is_active', 1);

            // Apply search if present
            if ($request->filled('q')) {
                $q->where('name', 'like', '%' . $request->q . '%');
            }

            // Always return simple array [{id, name}, ...]
            $subs = $q->select('id', 'name')->orderBy('name')->get();

            return response()->json($subs);
        } catch (\Throwable $e)
        {
            Log::error('Failed to fetch child-categories for category id ' . $subCategoryId, [
                'exception' => $e,
                'category_id' => $subCategoryId,
                'request_q' => $request->q ?? null,
            ]);

            return response()->json([
                'message' => 'Something went wrong while fetching child-categories.'
            ], 500);
        }
    }
}
