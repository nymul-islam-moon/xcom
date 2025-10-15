<?php

namespace App\Jobs;

use App\Models\Admin;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAdminWelcomeMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // retry/backoff settings (tweak as needed)
    public int $tries = 3;

    public $backoff = [10, 60, 300]; // seconds

    /**
     * Create a new job instance.
     */
    public function __construct(public int $adminId) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $admin = Admin::find($this->adminId);
        if (! $admin) {
            return;
        }

        // Already in a queued job → use send(), not queue()
        \Mail::to($admin->email)->send(new \App\Mail\AdminWelcomeMail($admin));

    }
}
