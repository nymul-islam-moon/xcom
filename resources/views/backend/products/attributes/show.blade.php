{{-- resources/views/admin/attributes/show.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Attribute Details')

@section('admin_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Attribute Details</h3>
                </div>
                <div class="col-sm-6">
                    <x-admin.breadcrumbs :items="[
                        ['label' => 'Home', 'route' => 'admin.dashboard', 'icon' => 'bi bi-house'],
                        ['label' => 'Product', 'route' => 'admin.products.index'],
                        ['label' => 'Attribute', 'route' => 'admin.products.attributes.index'],
                        ['label' => 'Details', 'active' => true],
                    ]" />
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">

            {{-- ===== Attribute Details Card ===== --}}
            <div class="row justify-content-center mb-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title mb-0">Attribute — {{ $attribute->name }}</h3>
                        </div>

                        <div class="card-body">


                            <dl class="row mb-0">
                                {{-- ID --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 border-bottom">ID</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 border-bottom mb-0">{{ $attribute->id }}</dd>

                                {{-- Name --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 border-bottom">Name</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 border-bottom mb-0 fw-semibold text-break">
                                    {{ $attribute->name }}
                                </dd>

                                {{-- Slug --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 border-bottom">Slug</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 border-bottom mb-0 text-break">
                                    {{ $attribute->slug ?? '—' }}
                                </dd>

                                {{-- Description (optional) --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 border-bottom">Description</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 border-bottom mb-0 text-break"
                                    style="white-space: pre-line;">
                                    @if (filled($attribute->description ?? null))
                                        {{ $attribute->description }}
                                    @else
                                        <em class="text-muted">Not provided</em>
                                    @endif
                                </dd>

                                {{-- Created --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 border-bottom">Created</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 border-bottom mb-0">
                                    {{ optional($attribute->created_at)->format('M d, Y h:i A') }}
                                </dd>

                                {{-- Last Updated --}}
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2">Last Updated</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 mb-0">
                                    {{ optional($attribute->updated_at)->format('M d, Y h:i A') }}
                                </dd>
                            </dl>
                        </div>

                        <div class="card-footer d-flex align-items-center">
                            <a href="{{ route('admin.products.attributes.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>

                            <div class="ms-auto d-flex align-items-center gap-2 flex-nowrap text-nowrap">
                                <a href="{{ route('admin.products.attributes.edit', $attribute) }}" class="btn btn-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form action="{{ route('admin.products.attributes.destroy', $attribute) }}" method="POST"
                                    class="d-inline-block m-0 p-0"
                                    onsubmit="return confirm('Delete this attribute? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div> <!-- /card -->
                </div>
            </div>

            {{-- ===== Attribute Values Table (like attributes index) ===== --}}
            <div class="row">
                <div class="col-lg-12">

                    <div class="card mb-4">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title flex-grow-1 mb-0">
                                Values for: <span class="fw-semibold">{{ $attribute->name }}</span>
                            </h3>
                            {{-- Search values (keeps on this show page) --}}
                            <form action="{{ route('admin.products.attributes.show', $attribute) }}" method="GET"
                                class="d-none d-sm-flex me-2">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                                        placeholder="Search value name/slug">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </form>

                            {{-- Create new value for this attribute --}}
                            <a href="{{ route('admin.products.attribute-values.create', $attribute) }}"
                                class="btn btn-sm btn-success">
                                <i class="bi bi-plus-lg"></i> Create Value
                            </a>
                        </div>

                        @if (session('value_success'))
                            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                                {{ session('value_success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('value_error'))
                            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                                {{ session('value_error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px">#</th>
                                            <th style="min-width: 220px;">Name</th>
                                            <th style="min-width: 200px;">Slug</th>
                                            <th style="width: 170px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($attributeValues as $idx => $value)
                                            <tr>
                                                <td>{{ $attributeValues->firstItem() + $idx }}</td>
                                                <td class="fw-semibold text-break">{{ $value->name }}</td>
                                                <td class="text-break">{{ $value->slug }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                                        <a href="{{ route('admin.products.attribute-values.show', [$attribute, $value]) }}"
                                                            class="btn btn-sm btn-outline-secondary" title="View">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.products.attribute-values.edit', [$value]) }}"
                                                            class="btn btn-sm btn-primary" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form
                                                            action="{{ route('admin.products.attribute-values.destroy', [$value]) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Delete this value? This action cannot be undone.');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                title="Delete">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">
                                                    No values found for this attribute.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer clearfix">
                            <div class="float-end">
                                {!! $attributeValues->appends(['q' => request('q')])->links('pagination::bootstrap-5') !!}
                            </div>
                        </div>
                    </div>

                </div> <!-- /.col -->
            </div> <!-- /.row -->

        </div> <!-- /.container-fluid -->
    </div> <!-- /.app-content -->
@endsection
