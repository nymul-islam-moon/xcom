{{-- resources/views/admin/attributes/create.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Create Attribute')

@section('admin_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Create Attribute</h3>
                </div>
                <div class="col-sm-6">
                    <x-admin.breadcrumbs :items="[
                        ['label' => 'Home', 'route' => 'admin.dashboard', 'icon' => 'bi bi-house'],
                        ['label' => 'Product', 'route' => 'admin.products.index'],
                        ['label' => 'Attribute', 'route' => 'admin.products.attributes.index'],
                        ['label' => 'Create', 'active' => true],
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
                        <div class="card-header">
                            <h3 class="card-title mb-0">Add New Attribute</h3>
                        </div>

                        @if (session('error'))
                            <div class="alert alert-danger m-3">{{ session('error') }}</div>
                        @endif

                        <div class="card-body">
                            <form action="{{ route('admin.products.attributes.store') }}" method="POST">
                                @csrf

                                {{-- Name --}}
                                <div class="mb-3">
                                    <label for="name" class="form-label">Attribute Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name') }}" placeholder="Enter attribute name" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Description (optional) --}}
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" id="description"
                                              class="form-control @error('description') is-invalid @enderror"
                                              placeholder="Optional attribute description">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Actions --}}
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.products.attributes.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Back
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle"></i> Create Attribute
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div> <!-- /card -->
                </div>
            </div>
        </div>
    </div>
@endsection
