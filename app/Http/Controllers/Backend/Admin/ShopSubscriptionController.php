<?php

namespace App\Http\Controllers\Backend\Admin;

use App\DataTables\Backend\ShopSubscriptionsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShopSubscriptionController extends Controller
{
    public function index(Shop $shop, ShopSubscriptionsDataTable $dataTable)
    {
        return $dataTable->with('shop_id', $shop->id)
            ->render('backend.admin.shops.subscription.index', compact('shop'));
    }


 

}
