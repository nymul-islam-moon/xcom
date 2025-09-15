{{-- resources/views/admin/brands/show.blade.php --}}
@extends('layouts.backend.app')

@section('title', 'Brand Details')

@section('admin_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Brand Details</h3>
                </div>
                <div class="col-sm-6">
                    <x-admin.breadcrumbs :items="[
                        ['label' => 'Home', 'route' => 'admin.dashboard', 'icon' => 'bi bi-house'],
                        ['label' => 'Product', 'route' => 'admin.products.index'],
                        ['label' => 'Brands', 'route' => 'admin.products.brands.index'],
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
                            <h3 class="card-title mb-0">Brand — {{ $brand->name }}</h3>
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
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 border-bottom">Name</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 border-bottom mb-0 fw-semibold text-break">
                                    {{ $brand->name }}
                                </dd>

                                {{-- Slug --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 border-bottom">Slug</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 border-bottom mb-0 text-break">
                                    {{ $brand->slug }}
                                </dd>

                                {{-- Status --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 border-bottom">Status</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 border-bottom mb-0">
                                    @if($brand->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </dd>

                                {{-- Image --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 border-bottom">Image</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 border-bottom mb-0">
                                    @if($brand->image)
                                        <div class="d-inline-flex flex-column align-items-end gap-2">
                                            <img src="{{ Storage::url($brand->image) }}"
                                                 alt="{{ $brand->name }} image"
                                                 class="img-thumbnail" style="max-height: 140px">
                                            <a href="{{ Storage::url($brand->image) }}" target="_blank" class="small">
                                                <i class="bi bi-box-arrow-up-right"></i> View full image
                                            </a>
                                        </div>
                                    @else
                                        <em class="text-muted">Not uploaded</em>
                                    @endif
                                </dd>

                                {{-- Description --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 border-bottom">Description</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 border-bottom mb-0 text-break" style="white-space: pre-line;">
                                    @if(filled($brand->description))
                                        {{ $brand->description }}
                                    @else
                                        <em class="text-muted">Not provided</em>
                                    @endif
                                </dd>

                                {{-- Created --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 border-bottom">Created</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 border-bottom mb-0">
                                    {{ optional($brand->created_at)->format('M d, Y h:i A') }}
                                </dd>

                                {{-- Last Updated --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2">Last Updated</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 mb-0">
                                    {{ optional($brand->updated_at)->format('M d, Y h:i A') }}
                                </dd>
                            </dl>
                        </div>

                        <div class="card-footer d-flex align-items-center">
                            <a href="{{ route('admin.products.brands.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>

                            <div class="ms-auto d-flex align-items-center gap-2 flex-nowrap text-nowrap">
                                <a href="{{ route('admin.products.brands.edit', $brand) }}" class="btn btn-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>

                                <form action="{{ route('admin.products.brands.destroy', $brand) }}"
                                      method="POST" class="d-inline-block m-0 p-0"
                                      onsubmit="return confirm('Delete this brand? This action cannot be undone.');">
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
