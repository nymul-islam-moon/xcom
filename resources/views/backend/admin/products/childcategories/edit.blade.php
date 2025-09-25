{{-- resources/views/admin/childcategories/edit.blade.php --}}
@extends('layouts.backend.app')

@section('title', 'Edit Childcategory')

@push('backend_styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endpush

@section('backend_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Edit Childcategory</h3>
                </div>
                <div class="col-sm-6">
                    <x-admin.breadcrumbs :items="[
                        ['label' => 'Home', 'route' => 'admin.dashboard', 'icon' => 'bi bi-house'],
                        ['label' => 'Product', 'route' => 'admin.products.index'],
                        ['label' => 'Child-Category', 'route' => 'admin.products.child-categories.index'],
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
                        <div class="card-header">
                            <h3 class="card-title mb-0">Update Childcategory</h3>
                        </div>

                        @if (session('error'))
                            <div class="alert alert-danger m-3">{{ session('error') }}</div>
                        @endif

                        <div class="card-body">
                            <form action="{{ route('admin.products.child-categories.update', $child_category->slug) }}" method="POST">
                                @csrf
                                @method('PUT')

                                {{-- Childcategory Name --}}
                                <div class="mb-3">
                                    <label for="name" class="form-label">Childcategory Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $child_category->name) }}"
                                        placeholder="Enter childcategory name" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @php
                                    $currentCategoryId = old('product_category_id') ?? ($child_category->productSubCategory->product_category_id ?? null);
                                    $currentCategoryName = old('product_category_name') ?? ($child_category->productSubCategory->productCategory->name ?? null);

                                    $currentSubCategoryId = old('product_sub_category_id', $child_category->product_sub_category_id ?? null);
                                    $currentSubCategoryName = old('product_sub_category_name') ?? ($child_category->productSubCategory->name ?? null);
                                @endphp

                                {{-- Product Category --}}
                                <div class="mb-3">
                                    <label for="product_category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select name="product_category_id" id="product_category_id"
                                        class="form-select @error('product_category_id') is-invalid @enderror" required>
                                        @if ($currentCategoryId && $currentCategoryName)
                                            <option value="{{ $currentCategoryId }}" selected>{{ $currentCategoryName }}</option>
                                        @elseif ($currentCategoryId)
                                            <option value="{{ $currentCategoryId }}" selected>Selected Category</option>
                                        @endif
                                    </select>
                                    @error('product_category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Product SubCategory --}}
                                <div class="mb-3">
                                    <label for="product_sub_category_id" class="form-label">Subcategory <span class="text-danger">*</span></label>
                                    <select name="product_sub_category_id" id="product_sub_category_id"
                                        class="form-select @error('product_sub_category_id') is-invalid @enderror" required>
                                        @if ($currentSubCategoryId && $currentSubCategoryName)
                                            <option value="{{ $currentSubCategoryId }}" selected>{{ $currentSubCategoryName }}</option>
                                        @elseif ($currentSubCategoryId)
                                            <option value="{{ $currentSubCategoryId }}" selected>Selected Subcategory</option>
                                        @endif
                                    </select>
                                    @error('product_sub_category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                 <div class="mb-3">
                                    <label for="is_active" class="form-label small">Select Status <span
                                            class="text-danger">*</span></label>
                                    <select name="is_active" id="child_category_status"
                                        class="form-select select2 @error('is_active') is-invalid @enderror" required>
                                        <!-- options loaded via AJAX -->
                                    </select>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Description --}}
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" id="description"
                                        class="form-control @error('description') is-invalid @enderror"
                                        placeholder="Optional childcategory description">{{ old('description', $child_category->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Actions --}}
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.products.child-categories.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Back
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle"></i> Update Childcategory
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(function() {
            const $category = $('#product_category_id');
            const $sub = $('#product_sub_category_id');

            const initialCategoryId = @json($currentCategoryId);
            const initialSubId = @json($currentSubCategoryId);

            const categoriesUrl = "{{ route('api.select-categories') }}";

            // Create a template URL on the server containing a placeholder we will replace in JS.
            // Use a unique placeholder string to avoid accidental collisions.
            const subCatUrlTemplate = "{{ route('api.select-sub-categories', ['categoryId' => 'CATEGORY_ID_PLACEHOLDER']) }}";

            function subCatUrlFor(categoryId) {
                return subCatUrlTemplate.replace('CATEGORY_ID_PLACEHOLDER', encodeURIComponent(categoryId));
            }

            // Category Select2
            $category.select2({
                placeholder: 'Select a category',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: categoriesUrl,
                    dataType: 'json',
                    delay: 200,
                    data: params => ({ q: params.term || '' }),
                    processResults: data => {
                        const arr = Array.isArray(data) ? data : (Array.isArray(data.data) ? data.data : []);
                        return { results: arr.map(c => ({ id: c.id, text: c.name })) };
                    }
                }
            });

            // Subcategory Select2 (depends on category)
            function initSubSelect(categoryId) {
                // destroy existing to avoid duplicate initializations
                if ($sub.hasClass('select2-hidden-accessible')) {
                    $sub.select2('destroy');
                }

                $sub.prop('disabled', !categoryId).val(null).trigger('change').html('');

                $sub.select2({
                    placeholder: categoryId ? 'Select a subcategory' : 'Select a category first',
                    allowClear: true,
                    width: '100%',
                    ajax: categoryId ? {
                        url: function() { return subCatUrlFor(categoryId); }, // <-- use URL builder
                        dataType: 'json',
                        delay: 200,
                        data: params => ({ q: params.term || '' }),
                        processResults: data => {
                            const arr = Array.isArray(data) ? data : (Array.isArray(data.data) ? data.data : []);
                            return { results: arr.map(c => ({ id: c.id, text: c.name })) };
                        }
                    } : undefined,
                    dropdownParent: $('body')
                });
            }

            // Init based on initial values
            initSubSelect(initialCategoryId);

            // Preselect sub if exists
            if (initialSubId && initialCategoryId) {
                const url = subCatUrlFor(initialCategoryId);
                $.getJSON(url, function(data) {
                    const arr = Array.isArray(data) ? data : (Array.isArray(data.data) ? data.data : []);
                    const found = arr.find(c => String(c.id) === String(initialSubId));
                    if (found) {
                        const opt = new Option(found.name, found.id, true, true);
                        $sub.append(opt).trigger('change');
                    } else {
                        const fallback = new Option(@json($currentSubCategoryName ?? null) || String(initialSubId), initialSubId, true, true);
                        $sub.append(fallback).trigger('change');
                    }
                }).fail(function() {
                    const fallback = new Option(@json($currentSubCategoryName ?? null) || String(initialSubId), initialSubId, true, true);
                    $sub.append(fallback).trigger('change');
                });
            }

            // When category changes, reload sub
            $category.on('change', function() {
                initSubSelect($(this).val());
            });

            // Clear sub when category cleared
            $category.on('select2:clear', function() {
                initSubSelect(null);
            });


            /**
             * Status Section
             **/
            const $status = $('#child_category_status');

            $status.select2({
                placeholder: 'Select status',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: "{{ route('api.select-status') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term || ''
                        };
                    },
                    processResults: function(data) {
                        // API may return either an array or { results: [...] }
                        const items = Array.isArray(data) ? data : (data.results || []);
                        const results = items.map(function(item) {
                            return {
                                id: String(item.id),
                                text: item.text ?? item.name ?? item.title ?? String(item.id)
                            };
                        });
                        return {
                            results: results
                        };
                    },
                    cache: true
                },
                dropdownParent: $('body')
            });


            // Preload selected value: prefer old('is_active') (validation), otherwise current model value
            const selectedValue = String(@json(old('is_active', (int) $child_category->is_active)));

            const selectedLabel = @json(old('is_active_label', null)) || (selectedValue === '1' ? 'Active' : (
                selectedValue === '0' ? 'Inactive' : null));

            if (selectedValue !== null && selectedValue !== '') {
                if (selectedLabel) {
                    // If we already have a label, append and mark selected
                    const option = new Option(selectedLabel, selectedValue, true, true);
                    $status.append(option).trigger('change');
                } else {
                    // Otherwise, attempt to fetch options from the API and find the matching label
                    $.ajax({
                        url: "{{ route('api.select-status') }}",
                        dataType: 'json'
                    }).then(function(data) {
                        const items = Array.isArray(data) ? data : (data.results || []);
                        const selected = items.find(item => String(item.id) === String(selectedValue));
                        if (selected) {
                            const label = selected.text ?? selected.name ?? selected.title ?? selectedValue;
                            const option = new Option(label, selected.id, true, true);
                            $status.append(option).trigger('change');
                        } else {
                            // final fallback when API doesn't contain the value
                            const fallback = new Option(selectedValue, selectedValue, true, true);
                            $status.append(fallback).trigger('change');
                        }
                    }).catch(function() {
                        const fallback = new Option(selectedValue, selectedValue, true, true);
                        $status.append(fallback).trigger('change');
                    });
                }
            }
        });
    </script>
@endpush

