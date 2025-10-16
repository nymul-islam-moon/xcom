<?php
// app/Jobs/SendShopVerificationEmail.php

namespace App\Jobs;

use App\Models\Shop;
use App\Notifications\VerifyShopEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendShopVerificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $shopId;

    public function __construct(int $shopId)
    {
        $this->shopId = $shopId;

        // Optional queue settings
        $this->onQueue('emails');
        $this->tries = 3;
    }

    public function handle()
    {
        $shop = Shop::find($this->shopId);

        if (! $shop) {
            Log::warning("SendShopVerificationEmail: shop #{$this->shopId} not found.");
            return;
        }

        // ✅ No need to check hasVerifiedEmail() since you're dispatching only when it's null

        $shop->notify(new VerifyShopEmail($shop));

        Log::info("SendShopVerificationEmail: verification email queued/sent for shop #{$this->shopId}.");
    }

    public function failed(\Throwable $exception)
    {
        Log::error("SendShopVerificationEmail failed for shop #{$this->shopId}: " . $exception->getMessage());
    }
}
