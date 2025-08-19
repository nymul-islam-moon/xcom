{{-- resources/views/mail/admin/welcome.blade.php --}}
@component('mail::message')
    # Welcome, {{ $admin->name }}!

    Your admin account has been created successfully.

    **Email:** {{ $admin->email }}

    @if (!empty($verificationUrl))
        Please verify your email to activate your account.

        @component('mail::button', ['url' => $verificationUrl])
            Verify Email
        @endcomponent
    @else
        @component('mail::button', ['url' => $loginUrl])
            Login to Admin Panel
        @endcomponent
    @endif

    If you didn’t request this account, please ignore this email.

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
