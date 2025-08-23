<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $productCategories = ProductCategory::limit(15)->orderBy('name', 'asc')->get();
        return view('frontend.index', compact('productCategories'));
    }
}
