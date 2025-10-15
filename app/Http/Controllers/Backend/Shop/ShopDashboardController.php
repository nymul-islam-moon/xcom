<?php

namespace App\Http\Controllers\Backend\Shop;

use App\Http\Controllers\Controller;

class ShopDashboardController extends Controller
{
    public function index()
    {
        return view('backend.dashboard');
    }
}
