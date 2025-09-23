{{-- resources/views/admin/subcategories/index.blade.php --}}
@extends('layouts.backend.app')

@section('title', 'Product Subcategories')

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
                    <h3 class="mb-0">Product Subcategories</h3>
                </div>
                <div class="col-sm-6">
                    <x-admin.breadcrumbs :items="[
                        ['label' => 'Home', 'route' => 'admin.dashboard', 'icon' => 'bi bi-house'],
                        ['label' => 'Product', 'route' => 'admin.products.index'],
                        ['label' => 'Subcategory', 'active' => true],
                    ]" />

                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card mb-4">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title flex-grow-1 mb-0">All Subcategories</h3>

                            {{-- Optional: simple search by name/slug/description --}}
                            <form action="{{ route('admin.products.sub-categories.index') }}" method="GET"
                                class="d-none d-sm-flex me-2">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                                        placeholder="Search name/slug/desc">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </form>

                            <a href="{{ route('admin.products.sub-categories.create') }}" class="btn btn-sm btn-success">
                                <i class="bi bi-plus-lg"></i> Create Subcategory
                            </a>
                        </div>

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="card-body">
                             {{ $dataTable->table() }}
                        </div>

                      
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@push('backend_scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>

    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush