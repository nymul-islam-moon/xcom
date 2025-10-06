<?php

namespace App\Http\Controllers\Backend\Shop\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;


class ShopAuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('backend.shop.auth.login');
    }

    public function store(Request $request)
    {
        // Validate form input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

       
        // Attempt login using 'shop' guard
        if (auth()->guard('shop')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            /** @var \App\Models\Shop $shop */
            $shop = auth()->guard('shop')->user();

            // Validate shop user
            $validationResult = $shop->validateShopUser();

            if ($validationResult !== true) {
                // If validation failed, log out and redirect back with error
                auth()->guard('shop')->logout();
                return back()->with('error', $validationResult)->withInput();
            }

            // All good — redirect to dashboard
            return redirect()->intended(route('shop.dashboard'));
        }

        // If login failed
        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->onlyInput('email');
    }


    public function destroy(Request $request)
    {
        Auth::guard('shop')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('shop.login');
    }
}
