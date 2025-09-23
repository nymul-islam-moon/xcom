{{-- resources/views/admin/subcategories/create.blade.php --}}
@extends('layouts.backend.app')

@section('title', 'Create Subcategory')

@push('backend_styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endpush

@section('backend_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Create Subcategory</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.products.categories.index') }}">Categories</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('admin.products.sub-categories.index') }}">Subcategories</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Create</li>
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
                            <h3 class="card-title mb-0">Add New Subcategory</h3>
                        </div>

                        @if (session('error'))
                            <div class="alert alert-danger m-3">{{ session('error') }}</div>
                        @endif

                        <div class="card-body">
                            <form action="{{ route('admin.products.sub-categories.store') }}" method="POST">
                                @csrf

                                {{-- Subcategory Name --}}
                                <div class="mb-3">
                                    <label for="name" class="form-label">Subcategory Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name"
                                        class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                        placeholder="Enter subcategory name" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Parent Category (AJAX via select2) --}}
                                <div class="mb-3">
                                    <label for="product_category_id" class="form-label">Category <span
                                            class="text-danger">*</span></label>
                                    <select name="product_category_id" id="product_category_id"
                                        class="form-select @error('product_category_id') is-invalid @enderror" required></select>
                                    @error('product_category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Dependent Subcategory (AJAX, loads after Category chosen) --}}
                                <div class="mb-3">
                                    <label for="product_sub_category_id" class="form-label">Subcategory <span
                                            class="text-danger">*</span></label>
                                    <select name="product_sub_category_id" id="product_sub_category_id"
                                        class="form-select @error('product_sub_category_id') is-invalid @enderror" required disabled></select>
                                    @error('product_sub_category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Description (optional) --}}
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                        placeholder="Optional subcategory description">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Actions --}}
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.products.sub-categories.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Back
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle"></i> Create Subcategory
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

@push('backend_scripts')
    {{-- If your layout already loads jQuery you can remove the next line; keeping it here is safe in most cases --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(function() {
            const $category = $('#product_category_id');
            const $subCategory = $('#product_sub_category_id');

            const oldCategoryId = @json(old('product_category_id'));
            const oldCategoryName = @json(old('product_category_name', ''));
            const oldSubCategoryId = @json(old('product_sub_category_id'));
            const oldSubCategoryName = @json(old('product_sub_category_name', ''));

            // Initialize Category select2 (identical pattern you used that works)
            $category.select2({
                placeholder: 'Select a category',
                allowClear: true,
                width: '100%',
                minimumInputLength: 0, // show all on open; still filter as you type
                ajax: {
                    url: "{{ route('api.select-categories') }}",
                    dataType: 'json',
                    delay: 200,
                    data: params => ({ q: params.term || '' }),
                    processResults: function(data, params) {
                        const term = (params && params.term ? params.term.toLowerCase() : '');
                        const filtered = term ?
                            data.filter(c => String(c.name).toLowerCase().includes(term)) :
                            data;

                        return {
                            results: filtered.map(c => ({
                                id: c.id,
                                text: c.name
                            }))
                        };
                    },
                    cache: true
                }
            });

            // Helper to build subcategories route (replace placeholder)
            function subCategoriesUrl(categoryId) {
                const stub = "{{ route('api.select-sub-categories', ['categoryId' => 'CATEGORY_ID']) }}";
                return stub.replace('CATEGORY_ID', encodeURIComponent(categoryId));
            }

            // Initialize Subcategory select2 with given URL
            function initSubCategorySelect(url) {
                // destroy existing Select2 so we can change ajax url reliably
                if ($subCategory.hasClass('select2-hidden-accessible')) {
                    $subCategory.select2('destroy');
                }

                $subCategory.select2({
                    placeholder: 'Select a subcategory',
                    allowClear: true,
                    width: '100%',
                    minimumInputLength: 0,
                    ajax: {
                        url: url,
                        dataType: 'json',
                        delay: 200,
                        data: params => ({ q: params.term || '' }),
                        processResults: function(data) {
                            const arr = Array.isArray(data) ? data : (Array.isArray(data.data) ? data.data : []);
                            return {
                                results: arr.map(i => ({ id: i.id, text: i.name || i.text || i.title }))
                            };
                        },
                        cache: true
                    }
                });
            }

            // When category changes -> enable/load subcategories
            $category.on('change', function () {
                const categoryId = $(this).val();

                // reset and disable
                $subCategory.val(null).trigger('change');
                $subCategory.prop('disabled', true);

                if (!categoryId) return;

                const url = subCategoriesUrl(categoryId);
                initSubCategorySelect(url);
                $subCategory.prop('disabled', false);
            });

            // Preselect old values if any (after validation error)
            if (oldCategoryId) {
                const option = new Option(oldCategoryName || oldCategoryId, oldCategoryId, true, true);
                $category.append(option).trigger('change');

                // init subcategory for this category and preselect old subcategory if present
                const url = subCategoriesUrl(oldCategoryId);
                initSubCategorySelect(url);
                $subCategory.prop('disabled', false);

                if (oldSubCategoryId) {
                    const subOption = new Option(oldSubCategoryName || oldSubCategoryId, oldSubCategoryId, true, true);
                    $subCategory.append(subOption).trigger('change');
                }
            }
        });
    </script>
@endpush
