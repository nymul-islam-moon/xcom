<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\ProductCategory;

class HomeController extends Controller
{
    public function index()
    {
        $productCategories = ProductCategory::limit(15)->orderBy('name', 'asc')->get();
        $brands = Brand::all();

        return view('frontend.index', compact('productCategories', 'brands'));
    }
}
