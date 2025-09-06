<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShopRequest;
use App\Http\Requests\UpdateShopRequest;
use App\Jobs\ScanShopsCsvAndQueueChunks;
use App\Jobs\ShopsCsvProcess;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Services\MediaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $term = $request->query('q', '');

        $shops = Shop::query()
            ->search($term)
            ->orderBy('name')
            ->paginate(15)
            ->appends(['q' => $term]);

        return view('admin.shops.index', compact('shops'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.shops.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreShopRequest $request)
    {
        try {
            $formData = $request->validated();

            // if ()

        } catch (\Exception $e)
        {

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Shop $shop)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shop $shop)
    {
        //
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
    public function destroy(Shop $shop)
    {
        //
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
            'bulk_file.required'   => 'Please choose a CSV file to upload.',
            'bulk_file.file'       => 'The upload must be a file.',
            'bulk_file.max'        => 'The file may not be greater than 20MB.',
            'bulk_file.mimes'      => 'Only .csv files are allowed.',
            'bulk_file.mimetypes'  => 'The provided file type is not recognized as CSV.',
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
                    'chunk_size'  => 5000,   // tune: 2000–10000
                    'header_row'  => true,
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
                ['bulk_file' => 'Upload failed: ' . $e->getMessage()],
                'bulkUpload'
            );
        }
    }
}
