<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
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
}
