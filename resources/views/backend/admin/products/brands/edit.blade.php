{{-- resources/views/admin/brands/edit.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Edit Brand')

@section('admin_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">Edit Brand</h3></div>
                <div class="col-sm-6">
                    <x-admin.breadcrumbs :items="[
                        ['label' => 'Home', 'route' => 'admin.dashboard', 'icon' => 'bi bi-house'],
                        ['label' => 'Product', 'route' => 'admin.products.index'],
                        ['label' => 'Brands', 'route' => 'admin.products.brands.index'],
                        ['label' => 'Edit', 'active' => true],
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
                        <div class="card-header"><h3 class="card-title mb-0">Update Brand — {{ $brand->name }}</h3></div>

                        @if (session('success'))
                            <div class="alert alert-success m-3">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger m-3">{{ session('error') }}</div>
                        @endif

                        <div class="card-body">
                            <form action="{{ route('admin.products.brands.update', $brand) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                {{-- Name --}}
                                <div class="mb-3">
                                    <label for="name" class="form-label">Brand Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $brand->name) }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Image --}}
                                <div class="mb-3">
                                    <label for="image" class="form-label">Image/Logo</label>
                                    <input type="file" name="image" id="image"
                                           class="form-control @error('image') is-invalid @enderror" accept="image/*">
                                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror

                                    @if(!empty($brand->image))
                                        <div class="mt-2">
                                            <div class="small text-muted mb-1">Current image:</div>
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($brand->image) }}"
                                                 alt="Brand Image" class="img-thumbnail" style="max-height: 120px">
                                        </div>
                                    @endif
                                    <div class="form-text">Leave empty to keep current image.</div>
                                </div>

                                {{-- Description --}}
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" id="description"
                                              class="form-control @error('description') is-invalid @enderror"
                                              placeholder="Optional brand description">{{ old('description', $brand->description) }}</textarea>
                                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Status --}}
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    @php $current = old('status', (string)($brand->status ?? '1')); @endphp
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="1" {{ $current === '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ $current === '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Actions --}}
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.products.brands.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Back
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Update Brand
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
