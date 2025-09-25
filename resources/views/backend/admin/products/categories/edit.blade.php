{{-- resources/views/admin/categories/edit.blade.php --}}
@extends('layouts.backend.app')

@section('title', 'Edit Category')

@push('backend_styles')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Small UI polish to make the card feel lighter and modern */
        .card {
            border-radius: .75rem;
            box-shadow: 0 6px 18px rgba(29, 31, 45, 0.06);
        }

        .card-header {
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.02), rgba(255, 255, 255, 0.01));
            border-bottom: none;
        }

        .form-label.small {
            font-size: .85rem;
        }

        .form-control:focus {
            box-shadow: 0 0 0 .15rem rgba(13, 110, 253, 0.12);
        }
    </style>
@endpush

@section('backend_content')
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Edit Category</h3>
                </div>
                <div class="col-sm-6">
                    <x-admin.breadcrumbs :items="[
                        ['label' => 'Home', 'route' => 'admin.dashboard', 'icon' => 'bi bi-house'],
                        ['label' => 'Product', 'route' => 'admin.products.index'],
                        ['label' => 'Category', 'route' => 'admin.products.categories.index'],
                        ['label' => 'Edit', 'active' => true],
                    ]" />
                </div>
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Category Edit Card -->
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <div>
                                <h3 class="card-title mb-0">Edit Category - {{ $category->name }}</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.products.categories.update', $category->slug) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- Category Name -->
                                <div class="mb-3">
                                    <label for="name" class="form-label small">Category Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" autofocus
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $category->name) }}" placeholder="Enter category name"
                                        required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Category Status (Select2 AJAX) -->
                                <div class="mb-3">
                                    <label for="is_active" class="form-label small">Select Status <span
                                            class="text-danger">*</span></label>
                                    <select name="is_active" id="category_status"
                                        class="form-select select2 @error('is_active') is-invalid @enderror" required>
                                        <!-- options loaded via AJAX -->
                                    </select>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <!-- Category Description -->
                                <div class="mb-3">
                                    <label for="description" class="form-label small">Description</label>
                                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                        placeholder="Optional category description" rows="5">{{ old('description', $category->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Submit Button -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('admin.products.categories.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Back
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Update Category
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <div class="small text-muted">
                                Created: {{ optional($category->created_at)->format('M d, Y h:i A') }}
                            </div>

                            <div class="small text-muted ms-auto">
                                Last updated: {{ optional($category->updated_at)->diffForHumans() ?? '—' }}
                            </div>
                        </div>

                    </div>
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
            const selectedValue = String(@json(old('is_active', (int) $category->is_active)));

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
