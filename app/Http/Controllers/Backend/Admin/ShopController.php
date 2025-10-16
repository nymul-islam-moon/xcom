<?php

namespace App\Http\Controllers\Backend\Admin;

use App\DataTables\Backend\ShopDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Admin\StoreShopRequest;
use App\Http\Requests\UpdateShopRequest;
use App\Jobs\ScanShopsCsvAndQueueChunks;
use App\Jobs\SendShopVerificationEmail;
use App\Models\Shop;
use App\Services\MediaService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ShopDataTable $dataTable)
    {

        return $dataTable->render('backend.admin.shops.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.admin.shops.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreShopRequest $request, MediaService $mediaService)
    {
        DB::beginTransaction();

        try {
            $formData = $request->validated();

            $formData['password'] = Hash::make(trim($formData['password']));

            // Handle shop logo upload using MediaService
            if ($path = $mediaService->storeFromRequest($request, 'shop_logo', 'shops/logos')) {
                $formData['shop_logo'] = $path;
            }

            // Handle shopkeeper photo upload using MediaService
            if ($path = $mediaService->storeFromRequest($request, 'shop_keeper_photo', 'shops/shopkeepers')) {
                $formData['shop_keeper_photo'] = $path;
            }

            // Create the shop
            $shop = Shop::create($formData);

            if ($shop->email_verified_at === null) {
                SendShopVerificationEmail::dispatch($shop->id);
            }

            DB::commit();

            return redirect()->route('admin.shops.index')
                ->with('success', 'Shop created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Shop creation failed: '.$e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong while creating the shop.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Shop $shop)
    {
        return view('backend.admin.shops.show', compact('shop'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shop $shop)
    {
        return view('backend.admin.shops.edit', compact('shop'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShopRequest $request, Shop $shop)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shop $shop, MediaService $mediaService)
    {
        DB::beginTransaction();

        try {
            // Delete shop files if exist
            $mediaService->deleteFile($shop->shop_logo);
            $mediaService->deleteFile($shop->shop_keeper_photo);

            // Delete the shop record
            $shop->delete();

            DB::commit();

            return redirect()->route('admin.shops.index')
                ->with('success', 'Shop deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Shop deletion failed: '.$e->getMessage());

            return redirect()->back()
                ->with('error', 'Something went wrong while deleting the shop.');
        }
    }

    /**
     * show bulk upload page
     */
    public function show_bulk_upload()
    {
        return view('admin.shops.bulk');
    }

    /**
     * Bulk Upload With csv, excel files
     */
    public function bulkUpload(Request $request, MediaService $mediaService)
    {
        $request->validateWithBag('bulkUpload', [
            'bulk_file' => [
                'required',
                'file',
                'max:20480', // 20 MB (value is in KB)
                'mimes:csv,txt',
                'mimetypes:text/plain,text/csv,application/csv,application/vnd.ms-excel',
            ],
        ], [
            'bulk_file.required' => 'Please choose a CSV file to upload.',
            'bulk_file.file' => 'The upload must be a file.',
            'bulk_file.max' => 'The file may not be greater than 20MB.',
            'bulk_file.mimes' => 'Only .csv files are allowed.',
            'bulk_file.mimetypes' => 'The provided file type is not recognized as CSV.',
        ]);

        $storedPath = null;

        try {
            $storedPath = $mediaService->storeFromRequest(
                $request,
                'bulk_file',
                directory: 'shops/imports',
                disk: 'local' // use 'public' if you want it accessible via /storage
            );

            if (! $storedPath) {
                return back()->withInput()->withErrors(
                    ['bulk_file' => 'The bulk file failed to upload.'],
                    'bulkUpload'
                );
            }

            // TODO: hand off to a job after commit if needed
            ScanShopsCsvAndQueueChunks::dispatch(
                path: $storedPath,
                options: [
                    'chunk_size' => 5000,   // tune: 2000–10000
                    'header_row' => true,
                    'insert_mode' => 'insert', // or 'upsert'
                ]
            )->onQueue('shops-low')->afterCommit();

            return redirect()->route('admin.shops.index');
        } catch (\Throwable $e) {
            // optionally delete the stored file if something after storing fails
            if ($storedPath) {
                $mediaService->deleteFile($storedPath, disk: 'public');
            }

            return back()->withInput()->withErrors(
                ['bulk_file' => 'Upload failed: '.$e->getMessage()],
                'bulkUpload'
            );
        }
    }


    public function send(Request $request, Shop $shop)
    {
        if ($shop->hasVerifiedEmail()) {
            return redirect()->back()->with('info', 'Shop email already verified.');
        }

        $shop->sendEmailVerificationNotification();

        return redirect()->back()->with('success', 'Verification email resent.');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'hash' => 'required|string',
        ]);

        $shop = Shop::findOrFail($request->id);

        // Prevent tampering: check that the hash matches the shop's email
        if (! hash_equals((string) $request->hash, sha1($shop->getEmailForVerification()))) {
            abort(403, 'Invalid verification link.');
        }

        if ($shop->hasVerifiedEmail()) {
            // Optional: redirect to a page telling "already verified"
            return redirect()->route('shop.login')->with('status', 'Email already verified.');
        }

        $shop->markEmailAsVerified();

        event(new Verified($shop));

        // Optionally, log the shop in immediately:
        // auth()->guard('shop')->login($shop);

        return redirect()->route('shop.login')->with('success', 'Email verified successfully.');
    }
}
