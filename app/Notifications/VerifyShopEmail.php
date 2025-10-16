<?php
// app/Notifications/VerifyShopEmail.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;                  // <<-- add this
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class VerifyShopEmail extends Notification implements ShouldQueue
{
    use Queueable; // <<-- add this

    public $shop;

    public function __construct($shop)
    {
        $this->shop = $shop;

        // Optional: set queue settings here if you want
        // $this->onQueue('emails');
        // $this->delay(now()->addSeconds(5));
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    protected function verificationUrl($notifiable)
    {
        $expiration = Carbon::now()->addMinutes(config('auth.verification.expire', 60));
        return URL::temporarySignedRoute(
            'shop.verification.verify',
            $expiration,
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    public function toMail($notifiable)
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verify Your Shop Email Address')
            ->greeting("Hello {$notifiable->name},")
            ->line('Please click the button below to verify your shop email address.')
            ->action('Verify Email Address', $url)
            ->line('This link will expire in ' . config('auth.verification.expire', 60) . ' minutes.')
            ->line('If you did not create a shop, no further action is required.');
    }
}
