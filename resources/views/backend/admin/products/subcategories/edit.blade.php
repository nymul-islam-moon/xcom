{{-- resources/views/admin/subcategories/edit.blade.php --}}
@extends('layouts.backend.app')

@section('title', 'Edit Subcategory')

@push('backend_styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endpush

@section('backend_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Edit Subcategory</h3>
                </div>
                <div class="col-sm-6">
                    <x-admin.breadcrumbs :items="[
                        ['label' => 'Home', 'route' => 'admin.dashboard', 'icon' => 'bi bi-house'],
                        ['label' => 'Product', 'route' => 'admin.products.index'],
                        ['label' => 'Sub-Category', 'route' => 'admin.products.sub-categories.index'],
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
                            <h3 class="card-title mb-0">Update Subcategory</h3>
                        </div>

                        @if (session('error'))
                            <div class="alert alert-danger m-3">{{ session('error') }}</div>
                        @endif

                        <div class="card-body">
                            <form action="{{ route('admin.products.sub-categories.update', $sub_category) }}"
                                method="POST">
                                @csrf
                                @method('PUT')

                                {{-- Subcategory Name --}}
                                <div class="mb-3">
                                    <label for="name" class="form-label">Subcategory Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $sub_category->name) }}" placeholder="Enter subcategory name"
                                        required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Category Status (Select2 AJAX) -->
                                <div class="mb-3">
                                    <label for="is_active" class="form-label">Select Status <span
                                            class="text-danger">*</span></label>
                                    <select name="is_active" id="is_active"
                                        class="form-select @error('is_active') is-invalid @enderror" required>
                                        {{-- Option(s) will be appended by JS (preloaded for current value) --}}
                                    </select>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Parent Category (AJAX via select2) --}}
                                <div class="mb-3">
                                    <label for="product_category_id" class="form-label">Category <span
                                            class="text-danger">*</span></label>
                                    <select name="product_category_id" id="product_category_id"
                                        class="form-select @error('product_category_id') is-invalid @enderror" required>
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
                                    @error('product_category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Description (optional) --}}
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                        placeholder="Optional subcategory description">{{ old('description', $sub_category->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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

@push('backend_scripts')
    {{-- Include jQuery only if your layout doesn't already load it. Keeping it is usually safe. --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(function() {
            const $category = $('#product_category_id');
            const $status = $('#is_active'); // correct id/name
            const selectedCategoryId = @json(old('product_category_id', $sub_category->product_category_id));
            const selectedStatusValue = @json(old('is_active', (int) $sub_category->is_active));
            const selectedStatusLabel = @json(old('is_active_label', null));

            // -----------------------
            // Status Select2 (via API)
            // -----------------------
            function initStatusSelect() {
                // Destroy if already initialized
                if ($status.hasClass('select2-hidden-accessible')) {
                    $status.select2('destroy');
                }

                $status.select2({
                    placeholder: 'Select status',
                    allowClear: true,
                    width: '100%',
                    minimumInputLength: 0,
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
                            // Support common shapes: [] or { results: [] } or { data: [] }
                            const items = Array.isArray(data) ? data : (data.results || data.data ||
                        []);
                            const results = Array.isArray(items) ? items.map(function(item) {
                                return {
                                    id: String(item.id),
                                    text: item.text ?? item.name ?? item.title ?? String(item
                                        .id)
                                };
                            }) : [];
                            return {
                                results: results
                            };
                        },
                        cache: true
                    },
                    dropdownParent: $('body'),
                    // hide the search when there are only a few options
                    minimumResultsForSearch: 10
                });
            }

            initStatusSelect();

            // Preselect status value:
            // 1) If we already have a label (passed via old), use it
            // 2) Otherwise, try to fetch label from API dataset and set it
            // 3) Fallback - append plain value as label
            (function preloadStatus() {
                if (selectedStatusValue === null || selectedStatusValue === undefined || selectedStatusValue ===
                    '') {
                    return;
                }

                // If the server passed label via old(), use it directly
                if (selectedStatusLabel) {
                    const opt = new Option(selectedStatusLabel, String(selectedStatusValue), true, true);
                    $status.append(opt).trigger('change');
                    return;
                }

                // Attempt to fetch options and find label
                $.ajax({
                    url: "{{ route('api.select-status') }}",
                    dataType: 'json'
                }).done(function(data) {
                    const items = Array.isArray(data) ? data : (data.results || data.data || []);
                    const found = (Array.isArray(items) ? items : []).find(item => String(item.id) ===
                        String(selectedStatusValue));
                    if (found) {
                        const label = found.text ?? found.name ?? found.title ?? String(found.id);
                        const opt = new Option(label, String(found.id), true, true);
                        $status.append(opt).trigger('change');
                    } else {
                        // fallback: use numeric value as label
                        const fallback = new Option(String(selectedStatusValue), String(
                            selectedStatusValue), true, true);
                        $status.append(fallback).trigger('change');
                    }
                }).fail(function() {
                    const fallback = new Option(String(selectedStatusValue), String(
                        selectedStatusValue), true, true);
                    $status.append(fallback).trigger('change');
                });
            })();

            // -----------------------
            // Category Select2 (unchanged)
            // -----------------------
            $category.select2({
                placeholder: 'Select a category',
                allowClear: true,
                width: '100%',
                minimumInputLength: 0,
                ajax: {
                    url: "{{ route('api.select-categories') }}",
                    dataType: 'json',
                    delay: 200,
                    data: params => ({
                        q: params.term || ''
                    }),
                    processResults: function(data, params) {
                        const term = (params && params.term ? params.term.toLowerCase() : '');
                        const arr = Array.isArray(data) ? data : (Array.isArray(data.data) ? data.data :
                            []);
                        const filtered = term ? arr.filter(c => String(c.name).toLowerCase().includes(
                            term)) : arr;
                        return {
                            results: filtered.map(c => ({
                                id: c.id,
                                text: c.name
                            }))
                        };
                    },
                    cache: true
                },
                dropdownParent: $('body')
            });

            // If there was no preloaded <option>, fetch and set the selected item (fallback)
            if (selectedCategoryId && !$category.find('option[value="' + selectedCategoryId + '"]').length) {
                $.getJSON("{{ route('api.select-categories') }}", function(data) {
                    const arr = Array.isArray(data) ? data : (Array.isArray(data.data) ? data.data : []);
                    const found = arr.find(c => String(c.id) === String(selectedCategoryId));
                    if (found) {
                        const option = new Option(found.name, found.id, true, true);
                        $category.append(option).trigger('change');
                    }
                });
            }
        });
    </script>
@endpush
