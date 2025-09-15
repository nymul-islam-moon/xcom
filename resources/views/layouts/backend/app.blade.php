<!doctype html>
<html lang="en">

<head>
    <x-admin.head-links />
    @stack('backend_styles')
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">

        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <!-- Left Navbar Links -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="bi bi-list"></i>
                        </a>
                    </li>
                   
                        <li class="nav-item d-none d-md-block">
                            <a href="#" class="nav-link">Home</a>
                        </li>
                 
                    <li class="nav-item d-none d-md-block">
                        <a href="#" class="nav-link">Contact</a>
                    </li>
                </ul>

                <!-- Right Navbar Links -->
                <ul class="navbar-nav ms-auto">
                    <!-- Search -->
                    <li class="nav-item">
                        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                            <i class="bi bi-search"></i>
                        </a>
                    </li>

                    <!-- User Menu Dropdown -->
                    @php
                        $user = null;
                        $guard = null;

                        if (auth()->guard('admin')->check()) {
                            $user = auth()->guard('admin')->user();
                            $guard = 'admin';
                        } elseif (auth()->guard('shop')->check()) {
                            $user = auth()->guard('shop')->user();
                            $guard = 'shop';
                        }
                    @endphp

                    @if ($user)
                        <li class="nav-item dropdown user-menu">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                <img src="{{ $user->photo ? Storage::url($user->photo) : asset('admin/assets/img/user2-160x160.jpg') }}"
                                    class="user-image rounded-circle shadow" alt="User Image" />
                                <span class="d-none d-md-inline">{{ $user->name }}</span>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                                <!-- User Header -->
                                <li class="user-header text-bg-primary p-3 text-center">
                                    <img src="{{ $user->photo ? Storage::url($user->photo) : asset('admin/assets/img/user2-160x160.jpg') }}"
                                        class="rounded-circle shadow mb-2" alt="User Image"
                                        style="width: 90px; height: 90px; object-fit: cover;">
                                    <p class="mb-0">
                                        {{ $user->name }}
                                        <small class="d-block text-light">
                                            {{ ucfirst($guard) }} Account
                                        </small>
                                    </p>
                                </li>

                                <!-- User Body (Optional Links) -->
                                <li class="user-body px-3 py-2">
                                    <div class="row text-center">
                                        <div class="col-4"><a href="#">Followers</a></div>
                                        <div class="col-4"><a href="#">Sales</a></div>
                                        <div class="col-4"><a href="#">Friends</a></div>
                                    </div>
                                </li>

                                <!-- User Footer -->
                                <li class="user-footer px-3 py-2 d-flex justify-content-between">
                                    {{-- <a href="{{ route($guard.'.profile') ?? '#' }}" class="btn btn-default btn-flat">Profile</a> --}}
                                    <a href="" class="btn btn-default btn-flat">Profile</a>

                                    <a href="#" class="btn btn-default btn-flat"
                                        onclick="event.preventDefault(); document.getElementById('{{ $guard }}-logout-form').submit();">
                                        Sign out
                                    </a>

                                    <form id="{{ $guard }}-logout-form" method="POST"
                                        action="{{ route($guard . '.logout') }}" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </nav>

        <x-admin.aside />

        <main class="app-main">
            @yield('admin_content')
        </main>

        <footer class="app-footer">
            <div class="float-end d-none d-sm-inline">Anything you want</div>
            <strong>
                Copyright &copy; 2014-2025&nbsp;
                <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.
            </strong>
            All rights reserved.
        </footer>
    </div>

    <x-admin.scripts />
    @stack('backend_scripts')
</body>

</html>
