{{-- resources/views/admin/childcategories/index.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Product Child Categories')

@section('admin_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Product Child Categories</h3>
                </div>
                <div class="col-sm-6">
                    <x-admin.breadcrumbs :items="[
                        ['label' => 'Home', 'route' => 'admin.dashboard', 'icon' => 'bi bi-house'],
                        ['label' => 'Product', 'route' => 'admin.products.index'],
                        ['label' => 'Child Category', 'active' => true],
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
                            <h3 class="card-title flex-grow-1 mb-0">All Child Categories</h3>

                            {{-- Optional: simple search by name/slug/description --}}
                            <form action="{{ route('admin.products.child-categories.index') }}" method="GET"
                                  class="d-none d-sm-flex me-2">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                                           placeholder="Search name/slug/desc">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </form>

                            <a href="{{ route('admin.products.child-categories.create') }}" class="btn btn-sm btn-success">
                                <i class="bi bi-plus-lg"></i> Create Child Category
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
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px">#</th>
                                            <th style="min-width: 220px;">Name</th>
                                            <th style="min-width: 200px;">Slug</th>
                                            <th style="min-width: 200px;">Category</th>
                                            <th style="min-width: 200px;">Subcategory</th>
                                            <th style="min-width: 260px;">Description</th>
                                            <th style="width: 110px;">Products</th>
                                            <th style="width: 170px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($productChildCategories as $idx => $childCategory)
                                            <tr>
                                                <td>{{ $productChildCategories->firstItem() + $idx }}</td>

                                                <td class="fw-semibold text-break">
                                                    {{ $childCategory->name }}
                                                </td>

                                                <td class="text-break">
                                                    {{ $childCategory->slug }}
                                                </td>

                                                {{-- Parent Category (via direct relation or through subcategory) --}}
                                                <td class="text-break">
                                                    {{ $childCategory->productSubCategory?->productCategory?->name ?? '—' }}
                                                </td>

                                                {{-- Parent Subcategory --}}
                                                <td class="text-break">
                                                    {{ $childCategory->productSubCategory?->name ?? '—' }}
                                                </td>

                                                <td>
                                                    <div class="text-truncate" style="max-width: 420px">
                                                        {{ $childCategory->description ?? '—' }}
                                                    </div>
                                                </td>

                                                <td>
                                                    <span class="badge bg-info">
                                                        {{ $childCategory->products_count ?? 0 }}
                                                    </span>
                                                </td>

                                                <td>
                                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                                        <a href="{{ route('admin.products.child-categories.show', $childCategory) }}"
                                                           class="btn btn-sm btn-outline-secondary" title="View">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.products.child-categories.edit', $childCategory) }}"
                                                           class="btn btn-sm btn-primary" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form action="{{ route('admin.products.child-categories.destroy', $childCategory) }}"
                                                              method="POST" class="d-inline"
                                                              onsubmit="return confirm('Delete this child category? This action cannot be undone.');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">
                                                    No child categories found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer clearfix">
                            <div class="float-end">
                                {!! $productChildCategories->appends(['q' => request('q')])->links('pagination::bootstrap-5') !!}
                            </div>
                        </div>
                    </div>

                </div> <!-- /.col -->
            </div> <!-- /.row -->
        </div> <!-- /.container-fluid -->
    </div> <!-- /.app-content -->
@endsection
