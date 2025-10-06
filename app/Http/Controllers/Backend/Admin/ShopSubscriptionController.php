<?php

namespace App\Http\Controllers\Backend\Admin;

use App\DataTables\Backend\ShopSubscriptionsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Admin\StoreShopPaymentRequest;
use App\Models\Account;
use App\Models\Shop;
use App\Models\ShopPayment;
use App\Models\ShopSuspension;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShopSubscriptionController extends Controller
{
    public function index(Shop $shop, ShopSubscriptionsDataTable $dataTable)
    {
        return $dataTable->with('shop_id', $shop->id)
            ->render('backend.admin.shops.subscription.index', compact('shop'));
    }

    public function create(Shop $shop)
    {
        $startDate = null;
        $latestPayment = $shop->payments()->latest()->first();
        if ($latestPayment) {
            $startDate = Carbon::parse($latestPayment->end_date)->format('Y-m-d');

        }

        return view('backend.admin.shops.subscription.create', compact('shop', 'startDate'));
    }

    public function store(StoreShopPaymentRequest $request, Shop $shop)
    {
        DB::beginTransaction();

        try {
            $formData = $request->validated();
            // dd($formData);
            $shopPayment = ShopPayment::create($formData);

            // for account table

            $formData = [
                'owner_id' => $formData['shop_id'],
                'owner_type' => \App\Models\Shop::class,
                'reference_type' => \App\Models\ShopPayment::class,
                'reference_id' => $shopPayment->id,
                'category' => 'payment',
                'sub_type' => 'subscription',
                'direction' => 'credit',
                'amount' => $formData['amount'],
                'currency' => $formData['currency'],
                'provider' => null,
                'transaction_id' => null,
                'payment_method' => $formData['payment_method'],
                'status' => 'completed',
                'happened_at' => now(),
            ];

            Account::create($formData);

            DB::commit();

            return redirect()->route('admin.shop-subscription.index', $shop->slug)
                ->with('success', 'Shop subscription activated');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Shop subscription failed: '.$e->getMessage());

            return redirect()->back()
                ->with('error', 'Something went wrong while subscription for shop.');
        }
    }


    public function destroy(ShopPayment $shopPayment)
    {
        DB::beginTransaction();

        try {
         
            $account = Account::where('owner_id', $shopPayment->shop->id)
                ->where('owner_type', 'App\Models\Shop')
                ->where('reference_type', 'App\Models\ShopPayment')
                ->where('reference_id', $shopPayment->id)
                ->first();

            $account->delete();

            $shopPayment->delete();

            DB::commit();

            return redirect()->route('admin.shop-subscription.index', $shopPayment->shop->slug)
                ->with('success', 'Shop Payment deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Shop Payment deletion failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Something went wrong while deleting the shop payment.');
        }
    }
}
