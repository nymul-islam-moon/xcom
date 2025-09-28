{{-- resources/views/admin/shops/show.blade.php --}}
@extends('layouts.backend.app')

@section('title', 'Shop Details')

@section('backend_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Shop Details</h3>
                </div>
                <div class="col-sm-6">
                    <x-admin.breadcrumbs :items="[
                        ['label' => 'Home', 'route' => 'admin.dashboard', 'icon' => 'bi bi-house'],
                        ['label' => 'Shops', 'route' => 'admin.shops.index'],
                        ['label' => 'Details', 'active' => true],
                    ]" />
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-8">

                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title mb-0">Shop — {{ $shop->name }}</h3>
                        </div>

                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            <dl class="row mb-0">

                                {{-- Name --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 border-bottom">Shop Name</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 border-bottom mb-0 fw-semibold text-break">
                                    {{ $shop->name }}
                                </dd>

                                {{-- Slug --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 border-bottom">Slug</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 border-bottom mb-0 text-break">
                                    {{ $shop->slug }}
                                </dd>

                                {{-- Email --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 border-bottom">Email</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 border-bottom mb-0 text-break">
                                    {{ $shop->email }}
                                </dd>

                                {{-- Phone --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 border-bottom">Phone</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 border-bottom mb-0 text-break">
                                    {{ $shop->phone ?? '-' }}
                                </dd>

                                {{-- Status --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 border-bottom">Status</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 border-bottom mb-0 text-break">
                                    @php
                                        $status = $shop->status ?? null;
                                        $statusLabels = [
                                            'pending' => 'secondary',
                                            'active' => 'success',
                                            'inactive' => 'warning',
                                            'suspended' => 'danger',
                                        ];
                                    @endphp

                                    @if ($status && isset($statusLabels[$status]))
                                        <span class="badge bg-{{ $statusLabels[$status] }}">
                                            {{ ucfirst($status) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </dd>


                                {{-- Shop Logo --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 border-bottom">Shop Logo</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 border-bottom mb-0">
                                    @if ($shop->shop_logo)
                                        <div class="d-inline-flex flex-column align-items-end gap-2">
                                            <img src="{{ Storage::url($shop->shop_logo) }}" alt="{{ $shop->name }} logo"
                                                class="img-thumbnail" style="max-height: 140px">
                                            <a href="{{ Storage::url($shop->shop_logo) }}" target="_blank" class="small">
                                                <i class="bi bi-box-arrow-up-right"></i> View full image
                                            </a>
                                        </div>
                                    @else
                                        <em class="text-muted">Not uploaded</em>
                                    @endif
                                </dd>
                                {{-- Shopkeeper --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 border-bottom">Shopkeeper</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 border-bottom mb-0">
                                    <div class="d-flex align-items-center justify-content-end gap-3 p-2 border rounded"
                                        style="max-width: 350px; margin-left: auto;">
                                        {{-- Shopkeeper photo --}}
                                        @if ($shop->shop_keeper_photo)
                                            <img src="{{ Storage::url($shop->shop_keeper_photo) }}" alt="Shopkeeper photo"
                                                class="rounded-circle border"
                                                style="width: 70px; height: 70px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle border d-flex align-items-center justify-content-center bg-light"
                                                style="width: 70px; height: 70px;">
                                                <i class="bi bi-person-fill text-muted fs-4"></i>
                                            </div>
                                        @endif

                                        {{-- Shopkeeper details --}}
                                        <div class="text-end">
                                            <h6 class="mb-1 fw-bold text-truncate" style="max-width: 200px;">
                                                {{ $shop->shop_keeper_name ?? '-' }}</h6>
                                            <div class="small text-muted">
                                                <div><i
                                                        class="bi bi-telephone-fill me-1"></i>{{ $shop->shop_keeper_phone ?? '-' }}
                                                </div>
                                                <div><i
                                                        class="bi bi-envelope-fill me-1"></i>{{ $shop->shop_keeper_email ?? '-' }}
                                                </div>
                                                <div><i class="bi bi-card-text me-1"></i>NID:
                                                    {{ $shop->shop_keeper_nid ?? '-' }}</div>
                                                <div><i class="bi bi-file-earmark-text-fill me-1"></i>TIN:
                                                    {{ $shop->shop_keeper_tin ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </dd>


                                {{-- Bank Info --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 border-bottom">Bank Info</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 border-bottom mb-0">
                                    <div class="d-flex flex-column align-items-end gap-1">
                                        <span><i class="bi bi-bank2"></i>
                                            <strong>{{ $shop->bank_name ?? '-' }}</strong></span>
                                        <span><i class="bi bi-wallet2"></i> Account:
                                            {{ $shop->bank_account_number ?? '-' }}</span>
                                        <span><i class="bi bi-geo-alt-fill"></i> Branch:
                                            {{ $shop->bank_branch ?? '-' }}</span>
                                    </div>
                                </dd>


                                {{-- Business Address --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 border-bottom">Address</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 border-bottom mb-0 text-break"
                                    style="white-space: pre-line;">
                                    {{ $shop->business_address ?? '-' }}
                                </dd>


                                {{-- Description --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 border-bottom">Description</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 border-bottom mb-0 text-break"
                                    style="white-space: pre-line;">
                                    {{ $shop->description ?? '-' }}
                                </dd>

                                {{-- Created --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 border-bottom">Created</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 border-bottom mb-0">
                                    {{ optional($shop->created_at)->format('M d, Y h:i A') }}
                                </dd>

                                {{-- Last Updated --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2">Last Updated</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 mb-0">
                                    {{ optional($shop->updated_at)->format('M d, Y h:i A') }}
                                </dd>

                            </dl>
                        </div>

                        <div class="card-footer d-flex align-items-center">
                            <a href="{{ route('admin.shops.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>

                            <div class="ms-auto d-flex align-items-center gap-2 flex-nowrap text-nowrap">
                                <a href="{{ route('admin.shops.edit', $shop) }}" class="btn btn-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>

                                <form action="{{ route('admin.shops.destroy', $shop) }}" method="POST"
                                    class="d-inline-block m-0 p-0"
                                    onsubmit="return confirm('Delete this shop? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div><!-- /card -->
                </div>
            </div>
        </div>
    </div>
@endsection
