<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('backend.admin.auth.login'); // make this view
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // go to intended admin page or dashboard
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->onlyInput('email');
    }

    public function destroy(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function verify(Request $request, $id, $hash)
    {
        $admin = Admin::findOrFail($id);

        // 1) signature/time validity
        if (! $request->hasValidSignature()) {
            abort(403, 'The verification link is invalid or has expired.');
        }

        // 2) email hash check (same as Laravel’s default)
        if (! hash_equals((string) $hash, sha1($admin->email))) {
            abort(403, 'Invalid verification hash.');
        }

        if ($admin->email_verified_at) {
            return redirect()->route('admin.login')->with('status', 'Email already verified.');
        }

        $admin->forceFill(['email_verified_at' => now()])->save();
        event(new Verified($admin));

        return redirect()->route('admin.login')->with('status', 'Email verified. You can log in now.');
    }
}
