{{-- resources/views/admin/users/edit.blade.php --}}
@extends('layouts.backend.app')

@section('title', 'Edit Admin')

@section('backend_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Edit Admin</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Admins</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @php
        $verifiedValue = old('email_verified_at');
        if (!$verifiedValue && $user->email_verified_at) {
            $verifiedValue = $user->email_verified_at->format('Y-m-d\TH:i');
        }
        $currentStatus = old('status', $user->status); // enum: active/inactive/suspended/pending
    @endphp

    <div class="app-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-8">

                    <div class="card mb-4">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title mb-0">Update Admin — {{ $user->name }}</h3>
                        </div>

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('admin.users.update', $user) }}" method="POST" novalidate>
                            @csrf
                            @method('PUT')

                            <div class="card-body">
                                {{-- Name --}}
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $user->name) }}" placeholder="Full name" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" id="email" name="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email', $user->email) }}" placeholder="admin@example.com" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Phone (required by schema) --}}
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                    <input type="text" id="phone" name="phone"
                                           class="form-control @error('phone') is-invalid @enderror"
                                           value="{{ old('phone', $user->phone) }}" placeholder="+8801XXXXXXXXX" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Status (enum) --}}
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select id="status" name="status"
                                            class="form-select @error('status') is-invalid @enderror" required>
                                        @foreach (['pending','active','inactive','suspended'] as $status)
                                            <option value="{{ $status }}" {{ $currentStatus === $status ? 'selected' : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Email Verified --}}
                                <div class="mb-3">
                                    <label for="email_verified_at" class="form-label">Email Verified At</label>
                                    <div class="input-group">
                                        <input type="datetime-local" id="email_verified_at" name="email_verified_at"
                                               class="form-control @error('email_verified_at') is-invalid @enderror"
                                               value="{{ $verifiedValue }}">
                                        <button type="button" class="btn btn-outline-secondary" id="setNowBtn">
                                            <i class="bi bi-clock"></i> Set Now
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" id="clearVerifiedBtn">
                                            <i class="bi bi-x-circle"></i> Clear
                                        </button>
                                        @error('email_verified_at')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text">Leave empty if the admin’s email isn’t verified yet.</div>
                                </div>

                                {{-- Password (optional change) --}}
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="password" class="form-label">New Password</label>
                                        <div class="input-group">
                                            <input type="password" id="password" name="password"
                                                   class="form-control @error('password') is-invalid @enderror"
                                                   placeholder="Leave blank to keep current">
                                            <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            @error('password')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-text">Fill only if you want to change the password.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                        <div class="input-group">
                                            <input type="password" id="password_confirmation" name="password_confirmation"
                                                   class="form-control" placeholder="Repeat new password">
                                            <button type="button" class="btn btn-outline-secondary" id="togglePassword2">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer d-flex align-items-center">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Back
                                </a>

                                <div class="ms-auto d-flex gap-2">
                                    <button type="reset" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Update Admin
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div> 
            </div>
        </div> 
    </div>

    @push('backend_scripts')
    <script>
        (function () {
            const toggle = (inputId, btnId) => {
                const input = document.getElementById(inputId);
                const btn   = document.getElementById(btnId);
                if (!input || !btn) return;
                btn.addEventListener('click', () => {
                    const show = input.type === 'password';
                    input.type = show ? 'text' : 'password';
                    btn.innerHTML = show ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
                });
            };
            toggle('password', 'togglePassword');
            toggle('password_confirmation', 'togglePassword2');

            const setNowBtn = document.getElementById('setNowBtn');
            const clearBtn  = document.getElementById('clearVerifiedBtn');
            const verified  = document.getElementById('email_verified_at');

            if (setNowBtn && verified) {
                setNowBtn.addEventListener('click', () => {
                    const now = new Date();
                    const pad = n => String(n).padStart(2, '0');
                    const fmt = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
                    verified.value = fmt;
                });
            }
            if (clearBtn && verified) {
                clearBtn.addEventListener('click', () => verified.value = '');
            }
        })();
    </script>
    @endpush
@endsection
