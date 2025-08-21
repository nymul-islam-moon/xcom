{{-- resources/views/admin/categories/show.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Child Category Details')

@section('admin_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Category Details</h3>
                </div>
                <div class="col-sm-6">
                    <x-admin.breadcrumbs :items="[
                        ['label' => 'Home', 'route' => 'admin.dashboard', 'icon' => 'bi bi-house'],
                        ['label' => 'Product', 'route' => 'admin.products.index'],
                        ['label' => 'Child Category', 'route' => 'admin.products.child-categories.index'],
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
                    <!-- Child Category Show Card -->
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title mb-0">Child Category — {{ $child_category->name }}</h3>


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
                                    {{ $child_category->name }}
                                </dd>

                                {{-- Slug --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3 border-bottom">
                                    Slug
                                </dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 border-bottom mb-0 text-break">
                                    {{ $child_category->slug ?? '—' }}
                                </dd>

                                {{-- Parent Category --}}

                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3 border-bottom">
                                    Parent Category
                                </dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 border-bottom mb-0 text-break">
                                    @if ($child_category->productSubCategory->productCategory)
                                        <a
                                            href="{{ route('admin.products.categories.show', $child_category->productSubCategory->productCategory) }}">
                                            {{ $child_category->productSubCategory->productCategory->name }}
                                        </a>
                                    @else
                                        <em class="text-muted">Not linked</em>
                                    @endif
                                </dd>

                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3 border-bottom">
                                    Parent Sub-Category
                                </dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 border-bottom mb-0 text-break">
                                    @if ($child_category->productSubCategory)
                                        <a
                                            href="{{ route('admin.products.sub-categories.show', $child_category->productSubCategory) }}">
                                            {{ $child_category->productSubCategory->name }}
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
                                    @if (filled($child_category->description))
                                        {{ $child_category->description }}
                                    @else
                                        <em class="text-muted">Not provided</em>
                                    @endif
                                </dd>

                                {{-- Products (optional) --}}
                                @isset($child_category->products_count)
                                    <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3 border-bottom">
                                        Products
                                    </dt>
                                    <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 border-bottom mb-0">
                                        <span class="badge text-bg-secondary">{{ $child_category->products_count }}</span>
                                    </dd>
                                @endisset

                                {{-- Created --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3 border-bottom">
                                    Created
                                </dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 border-bottom mb-0">
                                    {{ optional($child_category->created_at)->format('M d, Y h:i A') }}
                                </dd>

                                {{-- Last Updated --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3">
                                    Last Updated
                                </dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 mb-0">
                                    {{ optional($child_category->updated_at)->format('M d, Y h:i A') }}
                                </dd>
                            </dl>
                        </div>



                        <div class="card-footer d-flex align-items-center">
                            <a href="{{ route('admin.products.child-categories.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>

                            <div class="ms-auto d-flex align-items-center gap-2 flex-nowrap text-nowrap">
                                <a href="{{ route('admin.products.child-categories.edit', $child_category) }}"
                                    class="btn btn-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>

                                <form action="{{ route('admin.products.child-categories.destroy', $child_category) }}"
                                    method="POST" class="d-inline-block m-0 p-0"
                                    onsubmit="return confirm('Delete this category? This action cannot be undone.');">
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
