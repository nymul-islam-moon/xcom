{{-- resources/views/admin/admins/index.blade.php --}}
@extends('layouts.backend.app')

@section('title', 'Admin Users')

@section('backend_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Admin Users</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Admins</li>
                    </ol>
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
                            <h3 class="card-title flex-grow-1 mb-0">All Admins</h3>

                            <div class="ms-auto d-flex align-items-center gap-2">
                                {{-- (Optional) simple search by name/email --}}
                                <form action="{{ route('admin.users.index') }}" method="GET" class="d-none d-sm-flex">
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                                               placeholder="Search name/email">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="bi bi-search"></i>
                                        </button>
                                    </div>
                                </form>

                                <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-success">
                                    <i class="bi bi-plus-lg"></i> Create Admin
                                </a>
                            </div>
                        </div>

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px;">#</th>
                                            <th style="min-width: 180px;">Name</th>
                                            <th style="min-width: 220px;">Email</th>
                                            <th style="min-width: 140px;">Phone</th>
                                            <th style="width: 120px;">Verified</th>
                                            <th style="width: 110px;">Status</th>
                                            <th style="min-width: 160px;">Created</th>
                                            <th style="width: 170px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($admins as $idx => $admin)
                                            <tr>
                                                <td>{{ $admins->firstItem() + $idx }}</td>
                                                <td class="fw-semibold">{{ $admin->name }}</td>
                                                <td>
                                                    {{ $admin->email }}
                                                </td>
                                                <td>{{ $admin->phone ?? '—' }}</td>
                                                <td>
                                                    @if ($admin->email_verified_at)
                                                        <span class="badge bg-success">Verified</span>
                                                    @else
                                                        <span class="badge bg-secondary">Unverified</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($admin->status === 'active')
                                                        <span class="badge bg-primary">Active</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>{{ optional($admin->created_at)->format('M d, Y h:i A') }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <a href="{{ route('admin.users.show', $admin) }}"
                                                           class="btn btn-sm btn-outline-secondary" title="View">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.users.edit', $admin) }}"
                                                           class="btn btn-sm btn-primary" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form action="{{ route('admin.users.destroy', $admin) }}"
                                                              method="POST" class="d-inline"
                                                              onsubmit="return confirm('Delete this admin? This action cannot be undone.');">
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
                                                    No admins found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer clearfix">
                            <div class="float-end">
                                {!! $admins->appends(['q' => request('q')])->links('pagination::bootstrap-5') !!}
                            </div>
                        </div>
                    </div>

                </div> <!-- /.col -->
            </div> <!-- /.row -->
        </div> <!-- /.container-fluid -->
    </div> 
@endsection
