<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>

    {{-- Tailwind via CDN (fine for dev). For prod, switch to Vite --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- CSRF token for any JS use (form uses @csrf) --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="min-h-screen bg-gray-100 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div>
            <a href="/" class="flex items-center gap-2 text-gray-700">
                {{-- Replace with your logo image if you’d like --}}
                <svg class="w-12 h-12 text-gray-500" viewBox="0 0 48 48" fill="currentColor" aria-hidden="true">
                    <circle cx="24" cy="24" r="22" />
                </svg>
                <span class="font-semibold">Admin Panel</span>
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-white shadow-md sm:rounded-lg">
            {{-- Flash / status messages --}}
            @if (session('status'))
                <div class="mb-4 text-sm text-green-700 bg-green-50 border border-green-200 rounded p-3">
                    {{ session('status') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded p-3">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="mb-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded p-3">
                    <ul class="list-disc ml-4">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                  focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input id="password" name="password" type="password" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                  focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="remember"
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-600">Remember me</span>
                    </label>

                    @if (Route::has('admin.password.request'))
                        <a href="{{ route('admin.password.request') }}"
                           class="text-sm text-indigo-600 hover:text-indigo-700">Forgot your password?</a>
                    @endif
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium
                                   rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2
                                   focus:ring-offset-2 focus:ring-indigo-500">
                        Log in
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-6 text-xs text-gray-500">
            © {{ date('Y') }} Your Company
        </div>
    </div>
</body>
</html>
