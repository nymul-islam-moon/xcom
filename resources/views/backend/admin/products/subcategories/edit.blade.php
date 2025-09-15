{{-- resources/views/admin/subcategories/edit.blade.php --}}
@extends('layouts.backend.app')

@section('title', 'Edit Subcategory')

@push('admin_styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endpush

@section('admin_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Edit Subcategory</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.products.categories.index') }}">Categories</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.products.sub-categories.index') }}">Subcategories</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
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
                            <h3 class="card-title mb-0">Update Subcategory</h3>
                        </div>

                        @if (session('error'))
                            <div class="alert alert-danger m-3">{{ session('error') }}</div>
                        @endif

                        <div class="card-body">
                            <form action="{{ route('admin.products.sub-categories.update', $sub_category) }}" method="POST">
                                @csrf
                                @method('PUT')

                                {{-- Subcategory Name --}}
                                <div class="mb-3">
                                    <label for="name" class="form-label">Subcategory Name <span class="text-danger">*</span></label>
                                    <input
                                        type="text"
                                        name="name"
                                        id="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $sub_category->name) }}"
                                        placeholder="Enter subcategory name"
                                        required
                                    >
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Parent Category (AJAX via select2) --}}
                                <div class="mb-3">
                                    <label for="product_category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select
                                        name="product_category_id"
                                        id="product_category_id"
                                        class="form-select @error('product_category_id') is-invalid @enderror"
                                        required
                                    >
                                        @php
                                            $currentId = old('product_category_id', $sub_category->product_category_id);
                                        @endphp
                                        @if ($currentId)
                                            {{-- Preload current/old option so Select2 shows it before AJAX --}}
                                            <option value="{{ $currentId }}" selected>
                                                {{ old('product_category_name', $sub_category->productCategory->name ?? 'Current Category') }}
                                            </option>
                                        @endif
                                    </select>
                                    @error('product_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Description (optional) --}}
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea
                                        name="description"
                                        id="description"
                                        class="form-control @error('description') is-invalid @enderror"
                                        placeholder="Optional subcategory description"
                                    >{{ old('description', $sub_category->description) }}</textarea>
                                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Actions --}}
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.products.sub-categories.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Back
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Update Subcategory
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- End Card -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('admin_scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function () {
    const $select = $('#product_category_id');
    const selectedId = @json(old('product_category_id', $sub_category->product_category_id));

    $select.select2({
        placeholder: 'Select a category',
        allowClear: true,
        width: '100%',
        minimumInputLength: 0,
        ajax: {
            url: "{{ route('admin.products.select-categories') }}",
            dataType: 'json',
            delay: 200,
            data: params => ({ q: params.term || '' }),
            processResults: function (data, params) {
                const term = (params && params.term ? params.term.toLowerCase() : '');
                const filtered = term ? data.filter(c => String(c.name).toLowerCase().includes(term)) : data;
                return { results: filtered.map(c => ({ id: c.id, text: c.name })) };
            },
            cache: true
        }
    });

    // If there was no preloaded <option>, fetch and set the selected item (fallback)
    if (selectedId && !$select.find('option[value="'+selectedId+'"]').length) {
        $.getJSON("{{ route('admin.products.select-categories') }}", function (data) {
            const found = data.find(c => String(c.id) === String(selectedId));
            if (found) {
                const option = new Option(found.name, found.id, true, true);
                $select.append(option).trigger('change');
            }
        });
    }
});
</script>
@endpush
