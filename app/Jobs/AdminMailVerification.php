<?php

namespace App\Jobs;

use App\Models\Admin;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AdminMailVerification implements ShouldQueue
{
    use Queueable;

    public $admin;

    /**
     * Create a new job instance.
     */
    public function __construct(Admin $admin)
    {
        $this->admin = $admin;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Send the email verification notification
        \Log::info('Dispatching email verification to admin: '.$this->admin->email);
    }
}
