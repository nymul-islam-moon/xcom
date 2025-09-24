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
                    <x-admin.breadcrumbs :items="[
                        ['label' => 'Home', 'route' => 'admin.dashboard', 'icon' => 'bi bi-house'],
                        ['label' => 'Product', 'route' => 'admin.products.index'],
                        ['label' => 'Sub-Category', 'route' => 'admin.products.sub-categories.index'],
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
                                        class="form-select @error('product_category_id') is-invalid @enderror"
                                        required></select>
                                    @error('product_category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Status (is_active) - Select2 AJAX --}}
                                <div class="mb-3">
                                    <label for="is_active" class="form-label">Select Status <span
                                            class="text-danger">*</span></label>
                                    <select name="is_active" id="subcategory_status"
                                        class="form-select select2 @error('is_active') is-invalid @enderror" required>
                                        <!-- options loaded via AJAX -->
                                    </select>
                                    @error('is_active')
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function() {
            const $select = $('#product_category_id');
            const oldId = @json(old('product_category_id'));

            $select.select2({
                placeholder: 'Select a category',
                allowClear: true,
                width: '100%',
                minimumInputLength: 0, // show all on open; still filter as you type
                ajax: {
                    url: "{{ route('api.select-categories') }}",
                    dataType: 'json',
                    delay: 200,
                    // send the term (ok if your endpoint ignores it)
                    data: params => ({
                        q: params.term || ''
                    }),
                    // client-side filter so typing actually narrows the list
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

            // (Optional) Preselect old value after validation error
            if (oldId) {
                $.getJSON("{{ route('api.select-categories') }}", function(data) {
                    const found = data.find(c => String(c.id) === String(oldId));
                    if (found) {
                        const option = new Option(found.name, found.id, true, true);
                        $select.append(option).trigger('change');
                    }
                });
            }

            // ----------------------------
            // Status Select2 (is_active)
            // ----------------------------
            const $status = $('#subcategory_status');

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
                        // Normalize API data to Select2 format
                        const items = Array.isArray(data) ? data : (data.results || []);
                        const results = items.map(function(item) {
                            return {
                                id: item.id,
                                text: item.text ?? item.name ?? item.title ?? ''
                            };
                        });
                        return {
                            results: results
                        };
                    },
                    cache: true
                },
                dropdownParent: $('body') // ensures dropdown overlays correctly
            });

            // Preload old value for is_active (after validation error)
            const oldStatusValue = @json(old('is_active'));
            const oldStatusLabel = @json(old('is_active_label') ?? null);

            if (oldStatusValue) {
                if (oldStatusLabel) {
                    const option = new Option(oldStatusLabel, oldStatusValue, true, true);
                    $status.append(option).trigger('change');
                } else {
                    $.ajax({
                        url: "{{ route('api.select-status') }}",
                        dataType: 'json'
                    }).then(function(data) {
                        const items = Array.isArray(data) ? data : (data.results || []);
                        const selected = items.find(item => String(item.id) === String(oldStatusValue));
                        if (selected) {
                            const option = new Option(
                                selected.text ?? selected.name ?? selected.title,
                                selected.id, true, true
                            );
                            $status.append(option).trigger('change');
                        } else {
                            const fallback = new Option(oldStatusValue, oldStatusValue, true, true);
                            $status.append(fallback).trigger('change');
                        }
                    }).catch(function() {
                        const fallback = new Option(oldStatusValue, oldStatusValue, true, true);
                        $status.append(fallback).trigger('change');
                    });
                }
            }

        });
    </script>
@endpush
