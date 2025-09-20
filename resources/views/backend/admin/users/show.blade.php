{{-- resources/views/admin/admins/show.blade.php --}}
@extends('layouts.backend.app')

@section('title', 'Admin Details')

@section('backend_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Admin Details</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Admins</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Details</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-8">

                    <div class="card">
                        {{-- Header: title left, actions right --}}
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title mb-0">Admin — {{ $user->name }}</h3>

                            
                        </div>

                        {{-- Body: alerts + profile photo + details --}}
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            @php
                                // Try a few common fields for photo path/url
                                $rawPhoto = $user->photo ?? $user->avatar ?? $user->profile_photo_path ?? null;

                                $photoUrl = null;
                                if ($rawPhoto) {
                                    if (\Illuminate\Support\Str::startsWith($rawPhoto, ['http://','https://','/'])) {
                                        // Already a URL or absolute path
                                        $photoUrl = $rawPhoto;
                                    } elseif (\Illuminate\Support\Str::startsWith($rawPhoto, ['storage/', 'public/'])) {
                                        // Likely stored in public disk
                                        $photoUrl = \Illuminate\Support\Facades\Storage::url($rawPhoto);
                                    } else {
                                        // Generic storage path
                                        $photoUrl = \Illuminate\Support\Facades\Storage::url($rawPhoto);
                                    }
                                }
                                // Local placeholder (put an image at public/images/avatar-placeholder.png)
                                $photoUrl = $photoUrl ?: asset('images/avatar-placeholder.png');
                            @endphp

                            {{-- Profile header with large avatar --}}
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <img
                                    src="{{ $photoUrl }}"
                                    alt="{{ $user->name }}"
                                    class="rounded-circle border"
                                    style="width: 96px; height: 96px; object-fit: cover;"
                                    loading="lazy"
                                >
                                <div>
                                    <div class="fw-semibold fs-5">{{ $user->name }}</div>
                                    <div class="text-muted small">{{ $user->email }}</div>
                                    @if ($user->email_verified_at)
                                        <span class="badge bg-success mt-1">Verified</span>
                                    @else
                                        <span class="badge bg-secondary mt-1">Unverified</span>
                                    @endif
                                </div>

                                <div class="ms-auto">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-image"></i> Change Photo
                                    </a>
                                </div>
                            </div>

                            <dl class="row mb-0">
                                {{-- (Optional) Photo row as thumbnail inside details --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3 border-bottom">
                                    Profile Photo
                                </dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 border-bottom mb-0">
                                    <img
                                        src="{{ $photoUrl }}"
                                        alt="{{ $user->name }}"
                                        class="rounded-circle border"
                                        style="width: 48px; height: 48px; object-fit: cover;"
                                        loading="lazy"
                                    >
                                </dd>

                                {{-- Name --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3 border-bottom">Name</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 border-bottom mb-0 fw-semibold text-break">
                                    {{ $user->name }}
                                </dd>

                                {{-- Email --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3 border-bottom">Email</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 border-bottom mb-0 text-break">
                                    {{ $user->email }}
                                </dd>

                                {{-- Phone --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3 border-bottom">Phone</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 border-bottom mb-0 text-break">
                                    {{ $user->phone ?? '—' }}
                                </dd>

                                {{-- Verified --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3 border-bottom">Verified</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 border-bottom mb-0">
                                    @if ($user->email_verified_at)
                                        <span class="badge bg-success">Verified</span>
                                        <div class="small text-muted mt-1">
                                            {{ optional($user->email_verified_at)->format('M d, Y h:i A') }}
                                        </div>
                                    @else
                                        <span class="badge bg-secondary">Unverified</span>
                                    @endif
                                </dd>

                                {{-- Status --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3 border-bottom">Status</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 border-bottom mb-0">
                                    @if ($user->status === 'active')
                                        <span class="badge bg-primary">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </dd>

                                {{-- Created --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3 border-bottom">Created</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 border-bottom mb-0">
                                    {{ optional($user->created_at)->format('M d, Y h:i A') }}
                                </dd>

                                {{-- Last Updated --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3">Last Updated</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 mb-0">
                                    {{ optional($user->updated_at)->format('M d, Y h:i A') }}
                                </dd>
                            </dl>
                        </div>

                        {{-- Footer: Back left, actions right --}}
                        <div class="card-footer d-flex align-items-center">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                            <div class="ms-auto d-flex align-items-center gap-2 flex-nowrap text-nowrap">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form action="{{ route('admin.users.destroy', $user) }}"
                                      method="POST" class="d-inline-block m-0 p-0"
                                      onsubmit="return confirm('Delete this admin? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div> {{-- /.col --}}
            </div> {{-- /.row --}}
        </div> {{-- /.container-fluid --}}
    </div> {{-- /.app-content --}}
@endsection
