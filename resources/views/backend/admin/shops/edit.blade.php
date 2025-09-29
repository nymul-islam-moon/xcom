{{-- resources/views/admin/shops/edit.blade.php --}}
@extends('layouts.backend.app')

@section('title', 'Edit Shop')

@section('backend_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Edit Shop</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.shops.index') }}">Shops</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    {{-- =========================
                         EDIT FORM
                         ========================= --}}
                    <div class="card mb-4">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title mb-0">Edit Shop</h3>
                        </div>

                        <form action="{{ route('admin.shops.update', $shop->slug) }}" method="POST" enctype="multipart/form-data" novalidate>
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                {{-- Basic Info --}}
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Shop Name <span class="text-danger">*</span></label>
                                        <input type="text" id="name" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name', $shop->name) }}" placeholder="Shop name" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Shop Email <span class="text-danger">*</span></label>
                                        <input type="email" id="email" name="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email', $shop->email) }}" placeholder="shop@example.com" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Shop Phone</label>
                                        <input type="text" id="phone" name="phone"
                                            class="form-control @error('phone') is-invalid @enderror"
                                            value="{{ old('phone', $shop->phone) }}" placeholder="+8801XXXXXXXXX" maxlength="15">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <hr>

                                {{-- Shopkeeper --}}
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="shop_keeper_name" class="form-label">Shopkeeper Name <span class="text-danger">*</span></label>
                                        <input type="text" id="shop_keeper_name" name="shop_keeper_name"
                                            class="form-control @error('shop_keeper_name') is-invalid @enderror"
                                            value="{{ old('shop_keeper_name', $shop->shop_keeper_name) }}" placeholder="Full name" required>
                                        @error('shop_keeper_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="shop_keeper_phone" class="form-label">Shopkeeper Phone <span class="text-danger">*</span></label>
                                        <input type="text" id="shop_keeper_phone" name="shop_keeper_phone"
                                            class="form-control @error('shop_keeper_phone') is-invalid @enderror"
                                            value="{{ old('shop_keeper_phone', $shop->shop_keeper_phone) }}" placeholder="+8801XXXXXXXXX" required>
                                        @error('shop_keeper_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="shop_keeper_email" class="form-label">Shopkeeper Email</label>
                                        <input type="email" id="shop_keeper_email" name="shop_keeper_email"
                                            class="form-control @error('shop_keeper_email') is-invalid @enderror"
                                            value="{{ old('shop_keeper_email', $shop->shop_keeper_email) }}" placeholder="owner@example.com">
                                        @error('shop_keeper_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="shop_keeper_nid" class="form-label">Shopkeeper NID <span class="text-danger">*</span></label>
                                        <input type="text" id="shop_keeper_nid" name="shop_keeper_nid"
                                            class="form-control @error('shop_keeper_nid') is-invalid @enderror"
                                            value="{{ old('shop_keeper_nid', $shop->shop_keeper_nid) }}" placeholder="NID number" required>
                                        @error('shop_keeper_nid')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="shop_keeper_tin" class="form-label">Shopkeeper TIN <span class="text-danger">*</span></label>
                                        <input type="text" id="shop_keeper_tin" name="shop_keeper_tin"
                                            class="form-control @error('shop_keeper_tin') is-invalid @enderror"
                                            value="{{ old('shop_keeper_tin', $shop->shop_keeper_tin) }}" placeholder="TIN number" required>
                                        @error('shop_keeper_tin')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="dbid" class="form-label">DBID (Digital Business ID)</label>
                                        <input type="text" id="dbid" name="dbid"
                                            class="form-control @error('dbid') is-invalid @enderror"
                                            value="{{ old('dbid', $shop->dbid) }}" placeholder="e.g., DBID-12345">
                                        @error('dbid')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row g-3 mt-0">
                                    <div class="col-md-6">
                                        <label for="shop_keeper_photo" class="form-label">Shopkeeper Photo</label>
                                        <input type="file" id="shop_keeper_photo" name="shop_keeper_photo"
                                            class="form-control @error('shop_keeper_photo') is-invalid @enderror"
                                            accept="image/*">
                                        @error('shop_keeper_photo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Optional. JPG/PNG.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="shop_logo" class="form-label">Shop Logo</label>
                                        <input type="file" id="shop_logo" name="shop_logo"
                                            class="form-control @error('shop_logo') is-invalid @enderror"
                                            accept="image/*">
                                        @error('shop_logo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Optional. Square recommended.</div>
                                    </div>
                                </div>

                                <hr>

                                {{-- Bank --}}
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="bank_name" class="form-label">Bank Name</label>
                                        <input type="text" id="bank_name" name="bank_name"
                                            class="form-control @error('bank_name') is-invalid @enderror"
                                            value="{{ old('bank_name', $shop->bank_name) }}" placeholder="e.g., BRAC Bank">
                                        @error('bank_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="bank_account_number" class="form-label">Bank Account Number</label>
                                        <input type="text" id="bank_account_number" name="bank_account_number"
                                            class="form-control @error('bank_account_number') is-invalid @enderror"
                                            value="{{ old('bank_account_number', $shop->bank_account_number) }}" placeholder="Account number">
                                        @error('bank_account_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="bank_branch" class="form-label">Bank Branch</label>
                                        <input type="text" id="bank_branch" name="bank_branch"
                                            class="form-control @error('bank_branch') is-invalid @enderror"
                                            value="{{ old('bank_branch', $shop->bank_branch) }}" placeholder="Branch name">
                                        @error('bank_branch')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <hr>

                                {{-- Address & Description --}}
                                <div class="mb-3">
                                    <label for="business_address" class="form-label">Business Address</label>
                                    <textarea id="business_address" name="business_address" rows="2"
                                        class="form-control @error('business_address') is-invalid @enderror"
                                        placeholder="Full business address">{{ old('business_address', $shop->business_address) }}</textarea>
                                    @error('business_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea id="description" name="description" rows="3"
                                        class="form-control @error('description') is-invalid @enderror"
                                        placeholder="Short description about the shop">{{ old('description', $shop->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Email Verified At --}}
                                <div class="mb-3">
                                    <label for="email_verified_at" class="form-label">Email Verified At</label>
                                    <input type="datetime-local" id="email_verified_at" name="email_verified_at"
                                        class="form-control @error('email_verified_at') is-invalid @enderror"
                                        value="{{ old('email_verified_at', optional($shop->email_verified_at)->format('Y-m-d\TH:i')) }}">
                                    @error('email_verified_at')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="card-footer d-flex align-items-center">
                                <a href="{{ route('admin.shops.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Back
                                </a>
                                <div class="ms-auto d-flex gap-2">
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle"></i> Update Shop
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div> <!-- /.col -->
            </div> <!-- /.row -->
        </div> <!-- /.container-fluid -->
    </div> <!-- /.app-content -->
@endsection
