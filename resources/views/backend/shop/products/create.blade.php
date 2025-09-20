{{-- resources/views/admin/products/create.blade.php --}}
@extends('layouts.backend.app')

@section('title', 'Create Product')

@push('backend_styles')
    <!-- Select2 CSS (stable) -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .cke_notifications_area {
            display: none !important;
        }

        .card-footer .btn+.btn {
            margin-left: .5rem;
        }

        .select2-container--default .select2-selection--multiple .select2-search__field {
            min-height: 1.5em;
        }

        /* Improve Select2 dropdown z-index if needed */
        .select2-container {
            z-index: 2100;
        }

        .small-muted {
            font-size: .85rem;
            color: #6c757d;
        }
    </style>
@endpush

@section('backend_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Create New Product</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('shop.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('shop.products.index') }}">Products</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            {{-- SINGLE CREATE FORM CARD --}}
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title mb-0">Add Product</h3>
                </div>

                <form action="{{ route('shop.products.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="card-body">
                        {{-- Basic Product Info --}}
                        <div class="card card-primary card-outline mb-4">
                            <div class="card-header">
                                <div class="card-title">Basic Product Info
                                    <i class="bi bi-info-circle ms-2 text-primary" data-bs-toggle="tooltip"
                                        title="Fill product name, SKU, slug and descriptions. Slug auto-generates from name."></i>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Product Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name"
                                        class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                        required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Product Type</label>
                                        {{-- Use schema product_type values: physical, digital, subscription, service, gift_card --}}
                                        <select name="product_type" id="product_type" class="form-select">
                                            <option value="physical"
                                                {{ old('product_type') == 'physical' ? 'selected' : '' }}>Physical</option>
                                            <option value="digital"
                                                {{ old('product_type') == 'digital' ? 'selected' : '' }}>Digital</option>
                                            <option value="subscription"
                                                {{ old('product_type') == 'subscription' ? 'selected' : '' }}>Subscription
                                            </option>
                                            <option value="service"
                                                {{ old('product_type') == 'service' ? 'selected' : '' }}>Service</option>
                                            <option value="gift_card"
                                                {{ old('product_type') == 'gift_card' ? 'selected' : '' }}>Gift Card
                                            </option>
                                        </select>
                                        <div class="small-muted mt-1">Variant settings are only available for physical
                                            products.</div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Variant Type</label>
                                        {{-- variant_type: simple or variable. Only relevant for physical products. --}}
                                        <select name="variant_type" id="variant_type" class="form-select">
                                            <option value="simple"
                                                {{ old('variant_type', 'simple') == 'simple' ? 'selected' : '' }}>Simple
                                            </option>
                                            <option value="variable"
                                                {{ old('variant_type') == 'variable' ? 'selected' : '' }}>Variable</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4 sku-section">
                                        <label for="sku" class="form-label">SKU</label>
                                        <input type="text" name="sku" id="sku"
                                            class="form-control @error('sku') is-invalid @enderror"
                                            value="{{ old('sku') }}">
                                        @error('sku')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mt-3">
                                        <label for="slug" class="form-label">Slug</label>
                                        <input type="text" name="slug" id="slug"
                                            class="form-control @error('slug') is-invalid @enderror"
                                            value="{{ old('slug') }}">
                                        @error('slug')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3 mt-3">
                                    <label for="short_description" class="form-label">Short Description</label>
                                    <textarea name="short_description" id="short_description"
                                        class="form-control @error('short_description') is-invalid @enderror">{{ old('short_description') }}</textarea>
                                    @error('short_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Full Description</label>
                                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Category & Brand --}}
                        <div class="card card-success card-outline mb-4">
                            <div class="card-header">
                                <div class="card-title">Category & Brand</div>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="category_id" class="form-label">Category</label>
                                        <select name="category_id" id="category_id" class="form-select select2"
                                            data-placeholder="Select Category" data-old='@json(old('category_id'))'>
                                            <option value="">Select Category</option>
                                        </select>
                                        @error('category_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="subcategory_id" class="form-label">Subcategory</label>
                                        <select name="subcategory_id" id="subcategory_id" class="form-select select2"
                                            data-placeholder="Select Subcategory" data-old='@json(old('subcategory_id'))'>
                                            <option value="">Select Subcategory</option>
                                        </select>
                                        @error('subcategory_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="child_category_id" class="form-label">Child Category</label>
                                        <select name="child_category_id" id="child_category_id"
                                            class="form-select select2" data-placeholder="Select Child Category"
                                            data-old='@json(old('child_category_id'))'>
                                            <option value="">Select Child Category</option>
                                        </select>
                                        @error('child_category_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="brand_id" class="form-label">Brand</label>
                                        <select name="brand_id" id="brand_id" class="form-select select2"
                                            data-placeholder="Select Brand" data-old='@json(old('brand_id'))'>
                                            <option value="">Select Brand</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="status" class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>
                                                Active</option>
                                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                                Inactive</option>
                                            <option value="out_of_stock"
                                                {{ old('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock
                                            </option>
                                            <option value="discontinued"
                                                {{ old('status') == 'discontinued' ? 'selected' : '' }}>Discontinued
                                            </option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="is_featured" class="form-label">Featured?</label>
                                        <select name="is_featured" class="form-select">
                                            <option value="0" {{ old('is_featured') == '0' ? 'selected' : '' }}>No
                                            </option>
                                            <option value="1" {{ old('is_featured') == '1' ? 'selected' : '' }}>Yes
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Additional Schema Fields (Tax, Backorders, Identifiers, Publishing, Digital/Subs) --}}
                        <div class="card card-light card-outline mb-4">
                            <div class="card-header">
                                <div class="card-title">Advanced / Schema Fields</div>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Tax Included?</label>
                                        <select name="tax_included" class="form-select">
                                            <option value="1" {{ old('tax_included', 1) == 1 ? 'selected' : '' }}>Yes
                                            </option>
                                            <option value="0" {{ old('tax_included') == 0 ? 'selected' : '' }}>No
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="tax_percentage" class="form-label">Tax Percentage (%)</label>
                                        <input type="number" step="0.01" name="tax_percentage" id="tax_percentage"
                                            class="form-control" value="{{ old('tax_percentage') }}">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="allow_backorders" class="form-label">Backorders</label>
                                        <select name="allow_backorders" id="allow_backorders" class="form-select">
                                            <option value="no"
                                                {{ old('allow_backorders') == 'no' ? 'selected' : '' }}>No</option>
                                            <option value="notify"
                                                {{ old('allow_backorders') == 'notify' ? 'selected' : '' }}>Notify customer
                                            </option>
                                            <option value="yes"
                                                {{ old('allow_backorders') == 'yes' ? 'selected' : '' }}>Allow</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="restock_date" class="form-label">Restock Date</label>
                                        <input type="datetime-local" name="restock_date" id="restock_date"
                                            class="form-control" value="{{ old('restock_date') }}">
                                    </div>
                                </div>

                                <hr>

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="mpn" class="form-label">MPN</label>
                                        <input type="text" name="mpn" id="mpn" class="form-control"
                                            value="{{ old('mpn') }}">
                                    </div>

                                    <div class="col-md-4">
                                        <label for="gtin8" class="form-label">GTIN-8</label>
                                        <input type="text" name="gtin8" id="gtin8" class="form-control"
                                            value="{{ old('gtin8') }}">
                                    </div>

                                    <div class="col-md-4">
                                        <label for="gtin13" class="form-label">GTIN-13</label>
                                        <input type="text" name="gtin13" id="gtin13" class="form-control"
                                            value="{{ old('gtin13') }}">
                                    </div>

                                    <div class="col-md-4 mt-2">
                                        <label for="gtin14" class="form-label">GTIN-14</label>
                                        <input type="text" name="gtin14" id="gtin14" class="form-control"
                                            value="{{ old('gtin14') }}">
                                    </div>

                                    <div class="col-md-8 mt-2">
                                        <label for="return_policy" class="form-label">Return Policy</label>
                                        <textarea name="return_policy" id="return_policy" class="form-control">{{ old('return_policy') }}</textarea>
                                    </div>

                                    <div class="col-md-4 mt-2">
                                        <label for="return_days" class="form-label">Return Days</label>
                                        <input type="number" name="return_days" id="return_days" class="form-control"
                                            value="{{ old('return_days') }}">
                                    </div>
                                </div>

                                <hr>

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="publish_date" class="form-label">Publish Date</label>
                                        <input type="datetime-local" name="publish_date" id="publish_date"
                                            class="form-control" value="{{ old('publish_date') }}">
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label">Is Published?</label>
                                        <select name="is_published" class="form-select">
                                            <option value="0" {{ old('is_published') == 0 ? 'selected' : '' }}>No
                                            </option>
                                            <option value="1" {{ old('is_published') == 1 ? 'selected' : '' }}>Yes
                                            </option>
                                        </select>
                                    </div>


                                </div>
                                <br>
                                <div class="col-md-6">
                                    <label for="product_type_note" class="form-label">Notes</label>
                                    <div class="small-muted">Digital products accept download URL & license keys.
                                        Subscription products accept a subscription interval (ex: monthly, yearly).
                                    </div>
                                </div>
                                <hr>

                                <div class="row g-3">

                                    <div class="col-md-6 digital-fields">
                                        <label for="download_url" class="form-label">Download URL (for digital
                                            products)</label>
                                        <input type="url" name="download_url" id="download_url" class="form-control"
                                            value="{{ old('download_url') }}">
                                        @error('download_url')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 digital-fields">
                                        <label for="license_key" class="form-label">License Key (if applicable)</label>
                                        <input type="text" name="license_key" id="license_key" class="form-control"
                                            value="{{ old('license_key') }}">
                                    </div>

                                    <div class="col-md-6 subscription-fields mt-2">
                                        <label for="subscription_interval" class="form-label">Subscription
                                            Interval</label>
                                        <input type="text" name="subscription_interval" id="subscription_interval"
                                            class="form-control" placeholder="e.g. monthly, yearly"
                                            value="{{ old('subscription_interval') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Simple Pricing & Stock --}}
                        <div class="card card-secondary card-outline mb-4 simple-section">
                            <div class="card-header">
                                <div class="card-title">Pricing & Stock (Simple Product)</div>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="price" class="form-label">Regular Price</label>
                                        <input type="number" step="0.01" name="price" id="price"
                                            class="form-control" value="{{ old('price') }}">
                                        @error('price')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="sale_price" class="form-label">Sale Price</label>
                                        <input type="number" step="0.01" name="sale_price" id="sale_price"
                                            class="form-control" value="{{ old('sale_price') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="stock_quantity" class="form-label">Stock Quantity</label>
                                        <input type="number" name="stock_quantity" id="stock_quantity"
                                            class="form-control" value="{{ old('stock_quantity') }}">
                                    </div>
                                </div>

                                <hr>

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="weight" class="form-label">Weight (kg)</label>
                                        <input type="number" step="0.01" name="weight" id="weight"
                                            class="form-control" value="{{ old('weight') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="width" class="form-label">Width (cm)</label>
                                        <input type="number" step="0.01" name="width" id="width"
                                            class="form-control" value="{{ old('width') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="height" class="form-label">Height (cm)</label>
                                        <input type="number" step="0.01" name="height" id="height"
                                            class="form-control" value="{{ old('height') }}">
                                    </div>
                                </div>

                                <div class="row g-3 mt-2">
                                    <div class="col-md-4">
                                        <label for="depth" class="form-label">Depth (cm)</label>
                                        <input type="number" step="0.01" name="depth" id="depth"
                                            class="form-control" value="{{ old('depth') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="low_stock_threshold" class="form-label">Low Stock Threshold</label>
                                        <input type="number" step="1" name="low_stock_threshold"
                                            id="low_stock_threshold" class="form-control"
                                            value="{{ old('low_stock_threshold') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Images --}}
                        <div class="card card-info card-outline mb-4 simple-section">
                            <div class="card-header">
                                <div class="card-title">Main & Gallery Images</div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="main_image" class="form-label">Main Image (Thumbnail)</label>
                                    <input type="file" name="main_image" id="main_image"
                                        class="form-control @error('main_image') is-invalid @enderror" accept="image/*">
                                    @error('main_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Recommended size: 600x600px.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="gallery_images" class="form-label">Gallery Images</label>
                                    <input type="file" name="gallery_images[]" id="gallery_images"
                                        class="form-control @error('gallery_images') is-invalid @enderror"
                                        accept="image/*" multiple>
                                    @error('gallery_images')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Attributes (Variable) --}}
                        <div class="card card-warning card-outline mb-4 variable-section">
                            <div class="card-header">
                                <div class="card-title">Product Attributes</div>
                            </div>
                            <div class="card-body">
                                @if ($attributes->count() > 0)
                                    <div class="row g-3">
                                        @foreach ($attributes as $attribute)
                                            <div class="col-md-4">
                                                <label class="form-label">{{ $attribute->name }}</label>
                                                <select name="attribute_values[{{ $attribute->id }}][]"
                                                    class="form-select select2 attribute-select @error('attribute_values.' . $attribute->id) is-invalid @enderror"
                                                    multiple data-placeholder="Select {{ $attribute->name }}"
                                                    data-old='@json(old('attribute_values.' . $attribute->id, []))'>
                                                    @foreach ($attribute->values as $value)
                                                        <option value="{{ $value->id }}"
                                                            {{ in_array($value->id, old('attribute_values.' . $attribute->id, [])) ? 'selected' : '' }}>
                                                            {{ $value->value }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('attribute_values.' . $attribute->id)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-info mb-0">No attributes available. Please create attributes
                                        first.</div>
                                @endif
                            </div>
                        </div>

                        {{-- Combinations (Variable) --}}
                        <div class="card card-success card-outline mb-4 variable-section">
                            <div class="card-header">
                                <div class="card-title">Pricing, Stock & Images (Per Combination)</div>
                            </div>
                            <div class="card-body" id="combination-pricing">
                                <div class="alert alert-info mb-0">Select attribute values to generate combinations...
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer d-flex align-items-center">
                        <a href="{{ route('shop.products.index') }}" class="btn btn-secondary"><i
                                class="bi bi-arrow-left"></i> Back</a>
                        <div class="ms-auto d-flex gap-2">
                            <button type="reset" class="btn btn-outline-secondary"><i
                                    class="bi bi-arrow-counterclockwise"></i> Reset</button>
                            <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Create
                                Product</button>
                        </div>
                    </div>
                </form>
            </div>
            {{-- /.card --}}
        </div>
    </div>
@endsection

@push('backend_scripts')
    <!-- Select2 JS (stable) -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <!-- CKEditor -->
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Require jQuery in the layout — don't re-include jQuery here to avoid duplicates.
            if (typeof window.jQuery === 'undefined') {
                console.error('jQuery is required. Please include jQuery in your layout (e.g. jQuery 3.x).');
                return;
            }

            (function($) {
                // old combinations / validation errors for repopulation
                const oldCombinations = @json(old('combinations', []));
                const combinationErrors = @json($errors->getMessages());

                // Initialize a select as Select2 if not already initialized
                function initSelect2($el) {
                    if (!$el || !$el.length) return;
                    if ($el.hasClass('select2-hidden-accessible')) return; // already initialized
                    const isMultiple = $el.prop('multiple');
                    $el.select2({
                        width: '100%',
                        placeholder: $el.data('placeholder') || (isMultiple ? 'Select options' :
                            'Select an option'),
                        allowClear: !$el.prop('multiple'),
                        closeOnSelect: !isMultiple,
                        dropdownParent: $el.closest('.modal').length ? $el.closest('.modal') : $(
                            document.body)
                    });
                }

                // Populate select with items [{id,name},...] and set selected
                function populateSelect($el, items, selected) {
                    if (!$el || !$el.length) return;
                    // If single select, keep an empty option for placeholder
                    $el.empty();
                    if (!$el.prop('multiple')) {
                        $el.append('<option value=""></option>');
                    }
                    items.forEach(function(it) {
                        $el.append($('<option>', {
                            value: it.id,
                            text: it.name
                        }));
                    });
                    initSelect2($el);

                    if (selected !== undefined && selected !== null && selected !== '') {
                        $el.val(selected).trigger('change');
                    } else {
                        $el.trigger('change');
                    }
                }

                // Init all selects with class .select2
                $('.select2').each(function() {
                    initSelect2($(this));
                });

                // Restore old values stored in data-old attribute
                $('.select2').each(function() {
                    const $el = $(this);
                    const dataOld = $el.attr('data-old');
                    if (dataOld === undefined) return;

                    try {
                        const parsed = JSON.parse(dataOld);
                        if (Array.isArray(parsed)) {
                            if (parsed.length) $el.val(parsed).trigger('change');
                        } else if (parsed !== null && parsed !== '') {
                            $el.val(parsed).trigger('change');
                        }
                    } catch (e) {
                        // not JSON — treat as scalar
                        if (dataOld !== '') $el.val(dataOld).trigger('change');
                    }
                });

                // Load brands & categories via AJAX, then initialize Select2 & restore old selection
                $.get('{{ route('shop.select.brands') }}', function(data) {
                    populateSelect($('#brand_id'), data, @json(old('brand_id')));
                });

                $.get('{{ route('shop.select.categories') }}', function(data) {
                    populateSelect($('#category_id'), data, @json(old('category_id')));
                });

                // Subcategory & child chaining
                // Category -> Subcategory (no hide/show)
                $('#category_id').on('change', function() {
                    const id = $(this).val();
                    const $sub = $('#subcategory_id'),
                        $child = $('#child_category_id');

                    // clear selects but keep UI visible
                    $sub.val(null).trigger('change');
                    $sub.find('option').not(':disabled').remove();
                    $child.val(null).trigger('change');
                    $child.find('option').not(':disabled').remove();

                    initSelect2($sub);
                    initSelect2($child);

                    if (!id) return;

                    $.get('{{ route('shop.select.sub-categories') }}', {
                        category_id: id
                    }, function(data) {
                        // normalize to array if necessary
                        const items = Array.isArray(data) ? data : (data && data.data ? data
                            .data : []);
                        if (items && items.length) {
                            populateSelect($sub, items, @json(old('subcategory_id')));
                            // DO NOT hide/show wrappers here
                        } else {
                            // keep select cleared (no hide)
                            $sub.val(null).trigger('change');
                            $sub.find('option').not(':disabled').remove();
                        }
                    }).fail(function() {
                        console.error('Failed to fetch subcategories');
                        $sub.val(null).trigger('change');
                        $sub.find('option').not(':disabled').remove();
                    });
                });


                // Subcategory -> Child (no hide/show)
                $('#subcategory_id').on('change', function() {
                    const id = $(this).val();
                    const $child = $('#child_category_id');

                    // clear previous child options but don't hide
                    $child.val(null).trigger('change');
                    $child.find('option').not(':disabled').remove();
                    initSelect2($child);

                    if (!id) return;

                    $.get('{{ route('shop.select.child-categories') }}', {
                        subcategory_id: id
                    }, function(data) {
                        const items = Array.isArray(data) ? data : (data && data.data ? data
                            .data : []);
                        if (items && items.length) {
                            populateSelect($child, items, @json(old('child_category_id')));
                            // DO NOT hide/show wrappers here
                        } else {
                            // keep child cleared (no hide)
                            $child.val(null).trigger('change');
                            $child.find('option').not(':disabled').remove();
                        }
                    }).fail(function() {
                        console.error('Failed to fetch child categories');
                        $child.val(null).trigger('change');
                        $child.find('option').not(':disabled').remove();
                    });
                });



                // Initialize attribute selects (these are rendered server-side)
                $('.attribute-select').each(function() {
                    const $el = $(this);
                    initSelect2($el);
                    // old values already applied above by data-old handling
                });

                // Combination generator functions
                function generateCombinations(arrays) {
                    if (!arrays.length) return [
                        []
                    ];
                    const result = [];
                    const rest = generateCombinations(arrays.slice(1));
                    for (const value of arrays[0]) {
                        for (const combo of rest) {
                            result.push([value, ...combo]);
                        }
                    }
                    return result;
                }

                function loadCombinations() {
                    let attributeArrays = [];
                    let attributeNames = [];

                    $('.attribute-select').each(function() {
                        const values = $(this).val();
                        const name = $(this).closest('.col-md-4').find('label').text();
                        if (values && values.length > 0) {
                            attributeArrays.push(values);
                            attributeNames.push(name);
                        }
                    });

                    if (attributeArrays.length === 0) {
                        $('#combination-pricing').html(
                            '<div class="alert alert-info mb-0">Select attribute values to generate combinations...</div>'
                        );
                        return;
                    }

                    const combinations = generateCombinations(attributeArrays);
                    let html =
                        `<div class="table-responsive"><table class="table table-bordered align-middle"><thead class="table-light"><tr>`;
                    attributeNames.forEach(name => html += `<th>${name}</th>`);
                    // Added Default column header
                    html +=
                        `<th>Price</th><th>Sale Price</th><th>Stock</th><th>Slug</th><th>SKU</th><th>Images</th><th>Default</th></tr></thead><tbody>`;

                    combinations.forEach((combo, index) => {
                        html += `<tr>`;
                        combo.forEach(val => {
                            let text = $(`.attribute-select option[value="${val}"]`).text();
                            html += `<td>${text}</td>`;
                        });

                        let price = oldCombinations[index]?.price ?? '';
                        let sale = oldCombinations[index]?.sale_price ?? '';
                        let stock = oldCombinations[index]?.stock_quantity ?? '';
                        let slug = oldCombinations[index]?.slug ?? ''; // <-- new
                        let sku = oldCombinations[index]?.sku ?? '';

                        let priceErr = combinationErrors[`combinations.${index}.price`] ?
                            `<div class="invalid-feedback d-block">${combinationErrors[`combinations.${index}.price`][0]}</div>` :
                            '';
                        let saleErr = combinationErrors[`combinations.${index}.sale_price`] ?
                            `<div class="invalid-feedback d-block">${combinationErrors[`combinations.${index}.sale_price`][0]}</div>` :
                            '';
                        let stockErr = combinationErrors[`combinations.${index}.stock_quantity`] ?
                            `<div class="invalid-feedback d-block">${combinationErrors[`combinations.${index}.stock_quantity`][0]}</div>` :
                            '';
                        let slugErr = combinationErrors[`combinations.${index}.slug`] ?
                            `<div class="invalid-feedback d-block">${combinationErrors[`combinations.${index}.slug`][0]}</div>` :
                            '';
                        let defaultErr = combinationErrors[`combinations.${index}.is_default`] ?
                            `<div class="invalid-feedback d-block">${combinationErrors[`combinations.${index}.is_default`][0]}</div>` :
                            '';

                        html +=
                            `<td><input type="number" name="combinations[${index}][price]" value="${price}" class="form-control ${priceErr ? 'is-invalid' : ''}" step="0.01" placeholder="0.00">${priceErr}</td>`;
                        html +=
                            `<td><input type="number" name="combinations[${index}][sale_price]" value="${sale}" class="form-control ${saleErr ? 'is-invalid' : ''}" step="0.01" placeholder="0.00">${saleErr}</td>`;
                        html +=
                            `<td><input type="number" name="combinations[${index}][stock_quantity]" value="${stock}" class="form-control ${stockErr ? 'is-invalid' : ''}" placeholder="0">${stockErr}</td>`;
                        html +=
                            `<td><input type="text" name="combinations[${index}][slug]" value="${slug}" class="form-control ${slugErr ? 'is-invalid' : ''}" placeholder="slug">${slugErr}</td>`;
                        html +=
                            `<td><input type="text" name="combinations[${index}][sku]" value="${sku}" class="form-control" placeholder="sku"></td>`;
                        html += `<td>
                                    <div class="mb-2"><label class="form-label small">Main Image:</label><input type="file" name="combinations[${index}][main_image]" class="form-control" accept="image/*"></div>
                                    <div><label class="form-label small">Gallery Images:</label><input type="file" name="combinations[${index}][gallery_images][]" class="form-control" multiple accept="image/*"></div>
                                 </td>`;

                        // Hidden inputs for the attribute value ids
                        combo.forEach((val) => {
                            html +=
                                `<input type="hidden" name="combinations[${index}][attributes][]" value="${val}">`;
                        });

                        // Default checkbox — include a hidden field so unchecked becomes 0
                        const isDefaultChecked = oldCombinations[index]?.is_default ? 'checked' : '';
                        html += `<td>
                                    <input type="hidden" name="combinations[${index}][is_default]" value="0">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="combinations[${index}][is_default]" id="comb_default_${index}" value="1" ${isDefaultChecked}>
                                        <label class="form-check-label small" for="comb_default_${index}">Default</label>
                                    </div>
                                    ${defaultErr}
                                 </td>`;

                        html += `</tr>`;
                    });

                    html += `</tbody></table></div>`;
                    $('#combination-pricing').html(html);
                }

                // Listen attribute changes
                $(document).on('change', '.attribute-select', loadCombinations);

                // If there were old attribute_values, generate combos on load
                @if (old('attribute_values'))
                    loadCombinations();
                @endif

                // When the product type changes, reset variant_type to simple
                $('#product_type').on('change', function() {
                    // Reset variant_type to simple whenever product type changes
                    $('#variant_type').val('simple').trigger('change');
                    // toggleSections will be triggered by the combined binding below,
                    // but call it explicitly to ensure immediate UI update
                    toggleSections();
                });

                // Toggle simple/variable sections based on variant_type and product_type
                function toggleSections() {
                    const ptype = $('#product_type').val();
                    const vtype = $('#variant_type').val();

                    // Variant controls only for physical
                    if (ptype === 'physical') {
                        $('#variant_type').closest('.col-md-4').show();
                    } else {
                        // hide variant control for non-physical
                        $('#variant_type').closest('.col-md-4').hide();
                    }

                    if (ptype === 'physical' && vtype === 'variable') {
                        $('.simple-section').hide();
                        $('.sku-section').hide();
                        $('.variable-section').show();
                    } else {
                        // simple-mode (either non-physical products or simple variant)
                        $('.simple-section').show();
                        $('.sku-section').show();
                        $('.variable-section').hide();
                        $('#combination-pricing').html(
                            '<div class="alert alert-info mb-0">Select attribute values to generate combinations...</div>'
                        );
                    }

                    // show/hide digital & subscription specific fields
                    if (ptype === 'digital') {
                        $('.digital-fields').show();
                    } else {
                        $('.digital-fields').hide();
                    }

                    if (ptype === 'subscription') {
                        $('.subscription-fields').show();
                    } else {
                        $('.subscription-fields').hide();
                    }
                }
                // Keep existing binding for both controls
                $('#product_type, #variant_type').on('change', toggleSections);
                toggleSections();

                // Tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function(el) {
                    return new bootstrap.Tooltip(el);
                });

                // CKEditor
                try {
                    CKEDITOR.replace('short_description');
                    CKEDITOR.replace('description');
                    CKEDITOR.replace('return_policy');
                } catch (e) {
                    console.warn('CKEditor failed to init', e);
                }

                // Slug auto-generate
                $('#name').on('blur', function() {
                    const slug = $(this).val().toLowerCase().trim().replace(/[^a-z0-9\s-]/g, '')
                        .replace(/\s+/g, '-').replace(/-+/g, '-');
                    $('#slug').val(slug);
                });

                // Form reset: clear Select2 & CKEditor
                $('form').on('reset', function() {
                    setTimeout(function() {
                        $('.select2').each(function() {
                            $(this).val(null).trigger('change');
                        });
                        try {
                            for (const name in CKEDITOR.instances) CKEDITOR.instances[name]
                                .setData('');
                        } catch (e) {}
                        toggleSections();
                    }, 10);
                });
            })(jQuery);
        });
    </script>
@endpush
