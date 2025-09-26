{{-- resources/views/admin/attributes/show.blade.php --}}
@extends('layouts.backend.app')

@section('title', 'Attribute Details')

@push('backend_styles')
    <!-- DataTables CSS (Bootstrap5 integration) -->
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <!-- Optional plugins CSS -->
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('backend_content')
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
                            {{ $dataTable->table() }}
                        </div>
                    </div>

                </div> <!-- /.col -->
            </div> <!-- /.row -->

        </div> <!-- /.container-fluid -->
    </div> <!-- /.app-content -->
@endsection

@push('backend_scripts')
    <!-- DataTables core -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

    <!-- DataTables Bootstrap 5 integration -->
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

    <!-- Optional plugins (Buttons, Select) -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>

    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush
