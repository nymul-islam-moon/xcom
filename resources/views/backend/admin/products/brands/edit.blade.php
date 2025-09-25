{{-- resources/views/admin/brands/edit.blade.php --}}
@extends('layouts.backend.app')

@section('title', 'Edit Brand')

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
                            <form action="{{ route('admin.products.brands.update', $brand->slug) }}" method="POST" enctype="multipart/form-data">
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

                                 <div class="mb-3">
                                    <label for="is_active" class="form-label small">Select Status <span
                                            class="text-danger">*</span></label>
                                    <select name="is_active" id="brand_status"
                                        class="form-select select2 @error('is_active') is-invalid @enderror" required>
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
                                              placeholder="Optional brand description">{{ old('description', $brand->description) }}</textarea>
                                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                         <div class="card-footer d-flex align-items-center justify-content-between">
                            <div class="small text-muted">
                                Created: {{ optional($brand->created_at)->format('M d, Y h:i A') }}
                            </div>

                            <div class="small text-muted ms-auto">
                                Last updated: {{ optional($brand->updated_at)->diffForHumans() ?? '—' }}
                            </div>
                        </div>
                    </div> <!-- /card -->
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
            const $status = $('#brand_status');

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
            const selectedValue = String(@json(old('is_active', (int) $brand->is_active)));

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
