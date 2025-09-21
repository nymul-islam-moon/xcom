@extends('layouts.backend.app')

@section('title', 'Create Category')

@push('backend_styles')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endpush

@section('backend_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Create Category</h3>
                </div>
                <div class="col-sm-6">
                    <x-admin.breadcrumbs :items="[
                        ['label' => 'Home', 'route' => 'admin.dashboard', 'icon' => 'bi bi-house'],
                        ['label' => 'Product', 'route' => 'admin.products.index'],
                        ['label' => 'Category', 'route' => 'admin.products.categories.index'],
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
                    <!-- Category Create Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Add New Category</h3>
                        </div>
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        <div class="card-body">
                            <form action="{{ route('admin.products.categories.store') }}" method="POST">
                                @csrf
                                <!-- Category Name -->
                                <div class="mb-3">
                                    <label for="name" class="form-label">Category Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name"
                                        class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                        placeholder="Enter category name" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Category Status (Select2 AJAX) -->
                                <div class="mb-3">
                                    <label for="is_active" class="form-label">Select Status <span
                                            class="text-danger">*</span></label>
                                    <select name="is_active" id="category_status"
                                        class="form-select select2 @error('is_active') is-invalid @enderror" required>
                                        <!-- Option will be loaded via AJAX -->
                                    </select>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <!-- Category Description -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                        placeholder="Optional category description">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Submit Button -->
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.products.categories.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Back
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle"></i> Create Category
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
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {

            const $status = $('#category_status');

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

            // Preload old value
            const oldValue = @json(old('is_active'));
            const oldLabel = @json(old('is_active_label') ?? null);

            if (oldValue) {
                if (oldLabel) {
                    const option = new Option(oldLabel, oldValue, true, true);
                    $status.append(option).trigger('change');
                } else {
                    $.ajax({
                        url: "{{ route('api.select-status') }}",
                        dataType: 'json'
                    }).then(function(data) {
                        const items = Array.isArray(data) ? data : (data.results || []);
                        const selected = items.find(item => String(item.id) === String(oldValue));
                        if (selected) {
                            const option = new Option(
                                selected.text ?? selected.name ?? selected.title,
                                selected.id, true, true
                            );
                            $status.append(option).trigger('change');
                        } else {
                            const fallback = new Option(oldValue, oldValue, true, true);
                            $status.append(fallback).trigger('change');
                        }
                    }).catch(function() {
                        const fallback = new Option(oldValue, oldValue, true, true);
                        $status.append(fallback).trigger('change');
                    });
                }
            }

        });
    </script>
@endpush
