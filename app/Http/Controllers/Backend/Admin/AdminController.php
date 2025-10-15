<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\StoreAdminRequest;
use App\Http\Requests\Admin\Auth\UpdateAdminRequest;
use App\Jobs\AdminMailVerification;
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

        return view('backend.admin.users.index', compact('admins'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.admin.users.create');
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
            $data['email_verified_at'] = ! empty($data['email_verified_at']) ? $data['email_verified_at'] : null;

            // Ensure status becomes 0/1 (default active if omitted)
            $data['status'] = isset($data['status']) && $data['status'] == 1 ? 'active' : 'inactive';

            // Password will be hashed automatically by the Admin model cast
            $admin = Admin::create($data);
            DB::commit();

            // $admin->sendEmailVerificationNotification();
            // AdminMailVerification::dispatch($admin)->afterCommit();

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Admin created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Admin create failed: '.$e->getMessage());

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
        return view('backend.admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admin $user)
    {
        return view('backend.admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAdminRequest $request, Admin $user)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            // Normalize optionals
            $data['phone'] = $data['phone'] ?? null;

            // Only touch email_verified_at if present (empty => null)
            if (array_key_exists('email_verified_at', $data)) {
                $data['email_verified_at'] = $data['email_verified_at'] ?: null;
            }

            // ----- Status mapping to enum -----
            if (array_key_exists('status', $data)) {
                $allowed = ['active', 'inactive', 'suspended', 'pending'];

                // Support old checkbox 0/1 coming from Blade
                if ($data['status'] === 1 || $data['status'] === '1' || $data['status'] === true || $data['status'] === 'on') {
                    $data['status'] = 'active';
                } elseif ($data['status'] === 0 || $data['status'] === '0' || $data['status'] === false || $data['status'] === 'off') {
                    $data['status'] = 'inactive';
                } elseif (! in_array($data['status'], $allowed, true)) {
                    // If it's neither 0/1 nor a valid enum, don't change status
                    unset($data['status']);
                }
            }
            // -----------------------------------

            // Update password only if provided (model casts will hash)
            if (empty($data['password'])) {
                unset($data['password']);
            }

            // If email changes, reset verification unless explicitly provided
            $emailChanged = array_key_exists('email', $data) && $data['email'] !== $user->email;
            if ($emailChanged && ! array_key_exists('email_verified_at', $data)) {
                $data['email_verified_at'] = null;
            }

            $user->update($data);
            DB::commit();

            // // Optional: if email changed and is unverified, resend your welcome/verify mail
            // if ($emailChanged && is_null($user->email_verified_at)) {
            //     \App\Jobs\SendAdminWelcomeMail::dispatch($user->id)->afterCommit();
            // }

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Admin updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Admin update failed: '.$e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the admin.');
        }
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
            \Log::error('Admin delete failed: '.$e->getMessage());

            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Something went wrong while deleting the admin.');
        }
    }
}
