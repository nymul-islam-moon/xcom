<?php

namespace App\Http\Controllers\Backend\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShopDashboardController extends Controller
{
    public function index()
    {
        return view('backend.dashboard');
    }
}
