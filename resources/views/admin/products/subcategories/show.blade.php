{{-- resources/views/admin/subcategories/show.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Subcategory Details')

@section('admin_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Subcategory Details</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.products.categories.index') }}">Categories</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.products.sub-categories.index') }}">Subcategories</a>
                        </li>
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
                    <!-- Subcategory Show Card -->
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title mb-0">Subcategory — {{ $sub_category->name }}</h3>
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
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3 border-bottom">
                                    Name
                                </dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 border-bottom mb-0 fw-semibold text-break">
                                    {{ $sub_category->name }}
                                </dd>

                                {{-- Slug --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3 border-bottom">
                                    Slug
                                </dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 border-bottom mb-0 text-break">
                                    {{ $sub_category->slug ?? '—' }}
                                </dd>

                                {{-- Parent Category --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3 border-bottom">
                                    Parent Category
                                </dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 border-bottom mb-0 text-break">
                                    @if ($sub_category->productCategory)
                                        <a
                                            href="{{ route('admin.products.categories.show', $sub_category->productCategory) }}">
                                            {{ $sub_category->productCategory->name }}
                                        </a>
                                    @else
                                        <em class="text-muted">Not linked</em>
                                    @endif
                                </dd>


                                {{-- Description --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3 border-bottom">
                                    Description
                                </dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 border-bottom mb-0 text-break"
                                    style="white-space: pre-line;">
                                    @if (filled($sub_category->description))
                                        {{ $sub_category->description }}
                                    @else
                                        <em class="text-muted">Not provided</em>
                                    @endif
                                </dd>

                                {{-- Products (optional if you load withCount) --}}
                                @isset($sub_category->products_count)
                                    <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3 border-bottom">
                                        Products
                                    </dt>
                                    <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 border-bottom mb-0">
                                        <span class="badge text-bg-secondary">{{ $sub_category->products_count }}</span>
                                    </dd>
                                @endisset

                                {{-- Created --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3 border-bottom">
                                    Created
                                </dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 border-bottom mb-0">
                                    {{ optional($sub_category->created_at)->format('M d, Y h:i A') }}
                                </dd>

                                {{-- Last Updated --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3">
                                    Last Updated
                                </dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 mb-0">
                                    {{ optional($sub_category->updated_at)->format('M d, Y h:i A') }}
                                </dd>
                            </dl>
                        </div>

                        <div class="card-footer d-flex align-items-center">
                            <a href="{{ route('admin.products.sub-categories.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>

                            <div class="ms-auto d-flex align-items-center gap-2 flex-nowrap text-nowrap">
                                <a href="{{ route('admin.products.sub-categories.edit', $sub_category) }}"
                                    class="btn btn-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>

                                <form action="{{ route('admin.products.sub-categories.destroy', $sub_category) }}"
                                    method="POST" class="d-inline-block m-0 p-0"
                                    onsubmit="return confirm('Delete this subcategory? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- End Card -->
                </div>
            </div>
        </div>
    </div>
@endsection
