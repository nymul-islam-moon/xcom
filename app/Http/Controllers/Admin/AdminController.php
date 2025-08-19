<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\StoreAdminRequest;
use App\Http\Requests\Admin\Auth\UpdateAdminRequest;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = Admin::query()
            ->where('id', '!=', auth('admin')->id())
            ->paginate(5);  // Fetch all admins
        return view('admin.users.index', compact('admins')); // Return view with admins data
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAdminRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            // Normalize optional fields
            $data['phone'] = $data['phone'] ?? null;
            $data['email_verified_at'] = !empty($data['email_verified_at']) ? $data['email_verified_at'] : null;

            // Ensure status becomes 0/1 (default active if omitted)
            $data['status'] = isset($data['status']) && $data['status'] == 1 ? 'active' : 'inactive';

            // Password will be hashed automatically by the Admin model cast
            $admin = Admin::create($data);
            DB::commit();
            // Send welcome email
            \App\Jobs\SendAdminWelcomeMail::dispatch($admin->id)
                ->afterCommit();

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Admin created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Admin create failed: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Something went wrong while creating the admin.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admin $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAdminRequest $request, Admin $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $user)
    {
        try {
            $user->delete();

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Admin deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Admin delete failed: ' . $e->getMessage());
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Something went wrong while deleting the admin.');
        }
    }
}
