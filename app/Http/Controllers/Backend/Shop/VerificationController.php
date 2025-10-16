<?php

// app/Http/Controllers/Shop/VerificationController.php

namespace App\Http\Controllers\Backend\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'hash' => 'required|string',
        ]);

        $shop = Shop::findOrFail($request->id);

        if (! hash_equals((string) $request->hash, sha1($shop->getEmailForVerification()))) {
            abort(403, 'Invalid verification link.');
        }

        if ($shop->hasVerifiedEmail()) {
            return redirect()->route('shop.login')->with('status', 'Email already verified.');
        }

        $shop->markEmailAsVerified();
        event(new Verified($shop));

        return redirect()->route('shop.login')->with('success', 'Email verified successfully.');
    }
}
