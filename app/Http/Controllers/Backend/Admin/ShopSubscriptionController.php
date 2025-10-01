<?php

namespace App\Http\Controllers\Backend\Admin;

use App\DataTables\Backend\ShopSubscriptionsDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShopSubscriptionController extends Controller
{
    public function index(ShopSubscriptionsDataTable $dataTable)
    {
        return $dataTable->render('backend.admin.shops.subscription.index');
    }
}
