{{-- resources/views/shop/products/show.blade.php --}}
@extends('layouts.backend.app')

@section('title', 'Show Product')

@push('backend_styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .small-muted {
            font-size: .85rem;
            color: #6c757d;
        }

        .img-thumb {
            max-width: 220px;
            max-height: 220px;
            object-fit: cover;
            border-radius: .25rem;
        }

        .meta-label {
            font-weight: 600;
            color: #495057;
        }

        .meta-value {
            color: #212529;
        }

        .card-footer .btn+.btn {
            margin-left: .5rem;
        }

        .badge-blank {
            background: #f8f9fa;
            color: #6c757d;
            border: 1px solid #e9ecef;
        }

        pre.json {
            background: #f8f9fa;
            padding: 0.5rem;
            border-radius: 4px;
            font-size: .9rem;
        }
    </style>
@endpush

@section('backend_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Product Details</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('shop.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('shop.products.index') }}">Products</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Show</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            {{-- SINGLE SHOW CARD --}}
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title mb-0">{{ $product->name ?? '—' }}</h3>

                    <div class="ms-auto">
                        <a href="{{ route('shop.products.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                        <a href="{{ route('shop.products.edit', $product->id) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row gx-4 gy-4">
                        {{-- Left column: Images --}}
                        <div class="col-lg-4">
                            <div class="mb-3 text-center">
                                @if ($product->mainImage)
                                    <img src="{{ asset($product->mainImage->image_path) }}" alt="Main">
                                @else
                                    <div
                                        class="img-thumb d-flex align-items-center justify-content-center badge-blank mb-2">
                                        No Image
                                    </div>
                                @endif

                                <div class="small-muted">Recommended: 600×600</div>
                            </div>

                            {{-- Gallery --}}
                            <div class="mb-3">
                                <h6 class="mb-2">Gallery</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @if (isset($product->images) && $product->images->count() > 0)
                                        @foreach ($product->images as $img)
                                            <div>
                                                <img src="{{ asset($img->image_path ?? ($img->url ?? '')) }}" alt="gallery"
                                                    style="width:110px;height:110px;object-fit:cover;border-radius:.25rem;">
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="small-muted">No gallery images uploaded.</div>
                                    @endif
                                </div>
                            </div>

                            {{-- Basic meta --}}
                            <div>
                                <h6 class="mb-2">Quick Info</h6>
                                <p class="mb-1"><span class="meta-label">SKU:</span> <span
                                        class="meta-value">{{ $product->sku ?? '—' }}</span></p>
                                <p class="mb-1"><span class="meta-label">Slug:</span> <span
                                        class="meta-value">{{ $product->slug ?? '—' }}</span></p>
                                <p class="mb-1"><span class="meta-label">Status:</span> <span
                                        class="meta-value">{{ ucfirst($product->status ?? '—') }}</span></p>
                                <p class="mb-1"><span class="meta-label">Featured:</span> <span
                                        class="meta-value">{{ $product->is_featured ? 'Yes' : 'No' }}</span></p>
                                <p class="mb-0"><span class="meta-label">Type:</span> <span
                                        class="meta-value">{{ ucfirst(str_replace('_', ' ', $product->product_type ?? '—')) }}</span>
                                </p>
                            </div>
                        </div>

                        {{-- Right column: Details --}}
                        <div class="col-lg-8">
                            {{-- Basic Product Info --}}
                            <div class="card card-light card-outline mb-3">
                                <div class="card-header">
                                    <div class="card-title">Basic Product Info</div>
                                </div>
                                <div class="card-body">
                                    <p class="mb-2"><span class="meta-label">Name:</span> <span
                                            class="meta-value">{{ $product->name ?? '—' }}</span></p>
                                    <p class="mb-2"><span class="meta-label">Short Description:</span></p>
                                    <div class="mb-3 small-muted">{!! $product->short_description ? nl2br(e($product->short_description)) : '<span class="text-muted">—</span>' !!}</div>

                                    <p class="mb-2"><span class="meta-label">Full Description:</span></p>
                                    <div class="mb-3">{!! $product->description ? $product->description : '<span class="small-muted">—</span>' !!}</div>
                                </div>
                            </div>

                            {{-- Category & Brand --}}
                            <div class="card card-light card-outline mb-3">
                                <div class="card-header">
                                    <div class="card-title">Category & Brand</div>
                                </div>
                                <div class="card-body row g-3">
                                    <div class="col-md-4">
                                        <p class="mb-0"><span class="meta-label">Category:</span></p>
                                        <div class="meta-value">{{ optional($product->category)->name ?? '—' }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="mb-0"><span class="meta-label">Subcategory:</span></p>
                                        <div class="meta-value">{{ optional($product->subCategory)->name ?? '—' }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="mb-0"><span class="meta-label">Child Category:</span></p>
                                        <div class="meta-value">{{ optional($product->childCategory)->name ?? '—' }}</div>
                                    </div>

                                    <div class="col-md-6 mt-2">
                                        <p class="mb-0"><span class="meta-label">Brand:</span></p>
                                        <div class="meta-value">{{ optional($product->brand)->name ?? '—' }}</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Advanced / Schema Fields --}}
                            <div class="card card-light card-outline mb-3">
                                <div class="card-header">
                                    <div class="card-title">Advanced / Schema</div>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <p class="mb-0"><span class="meta-label">Tax Included?</span></p>
                                            <div class="meta-value">{{ $product->tax_included ? 'Yes' : 'No' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="mb-0"><span class="meta-label">Tax %</span></p>
                                            <div class="meta-value">{{ $product->tax_percentage ?? '—' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="mb-0"><span class="meta-label">Allow Backorders</span></p>
                                            <div class="meta-value">{{ ucfirst($product->allow_backorders ?? 'no') }}
                                            </div>
                                        </div>

                                        <div class="col-md-4 mt-3">
                                            <p class="mb-0"><span class="meta-label">MPN</span></p>
                                            <div class="meta-value">{{ $product->mpn ?? '—' }}</div>
                                        </div>
                                        <div class="col-md-4 mt-3">
                                            <p class="mb-0"><span class="meta-label">GTIN-13</span></p>
                                            <div class="meta-value">{{ $product->gtin13 ?? '—' }}</div>
                                        </div>
                                        <div class="col-md-4 mt-3">
                                            <p class="mb-0"><span class="meta-label">Return Days</span></p>
                                            <div class="meta-value">{{ $product->return_days ?? '—' }}</div>
                                        </div>

                                        <div class="col-12 mt-3">
                                            <p class="mb-0"><span class="meta-label">Return Policy</span></p>
                                            <div class="meta-value">{!! $product->return_policy ? nl2br(e($product->return_policy)) : '<span class="small-muted">—</span>' !!}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div> {{-- end right col --}}
                    </div> {{-- end top row --}}


                    {{-- Attributes --}}
                    <div class="card card-warning card-outline mb-3 variable-section">
                        <div class="card-header">
                            <div class="card-title">Attributes</div>
                        </div>
                        <div class="card-body">
                            @php
                                // Try to display attributes: check common relation names
                                $displayed = false;
                            @endphp

                            @if (isset($product->attributes) && $product->attributes->count())
                                <div class="row g-3">
                                    @foreach ($product->attributes as $attr)
                                        <div class="col-md-4">
                                            <p class="mb-1 meta-label">{{ $attr->name }}</p>
                                            <div class="meta-value">
                                                @if (isset($attr->pivot) && isset($attr->pivot->value))
                                                    {{ $attr->pivot->value }}
                                                @elseif(isset($attr->values))
                                                    {{ $attr->values->pluck('value')->join(', ') }}
                                                @else
                                                    —
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @php $displayed = true; @endphp
                            @endif

                            {{-- fallback: show variant attribute values if product.attributes not present --}}
                            @if (!$displayed && isset($product->variants) && $product->variants->count())
                                <div class="small-muted mb-2">Attributes are shown per variant below.</div>
                            @endif

                            @if (!$displayed && (!isset($product->attributes) || $product->attributes->count() === 0))
                                <div class="small-muted">No attributes available.</div>
                            @endif
                        </div>
                    </div>

                    {{-- Combinations / Variants --}}
                    <div class="card card-success card-outline mb-4 variable-section">
                        <div class="card-header">
                            <div class="card-title">Combinations / Variants</div>
                        </div>
                        <div class="card-body">
                            @if (isset($product->variants) && $product->variants->count())
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Attributes</th>
                                                <th>Price</th>
                                                <th>Sale Price</th>
                                                <th>Stock</th>
                                                <th>SKU</th>
                                                <th>Slug</th>
                                                <th>Main Image</th>
                                                <th>Default</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($product->variants as $idx => $variant)
                                                <tr>
                                                    <td>{{ $idx + 1 }}</td>
                                                    <td>
                                                        @php
                                                            // Try several common ways of retrieving attribute values from the variant
                                                            $attrTexts = [];
                                                            if (
                                                                isset($variant->attributeValues) &&
                                                                $variant->attributeValues->count()
                                                            ) {
                                                                foreach ($variant->attributeValues as $av) {
                                                                    $attrTexts[] =
                                                                        (optional($av->attribute)->name
                                                                            ? optional($av->attribute)->name . ': '
                                                                            : '') .
                                                                        ($av->value ??
                                                                            ($av->display_value ?? ($av->name ?? '—')));
                                                                }
                                                            } elseif (
                                                                isset($variant->attributes) &&
                                                                $variant->attributes->count()
                                                            ) {
                                                                foreach ($variant->attributes as $a) {
                                                                    $attrTexts[] =
                                                                        $a->name .
                                                                        ': ' .
                                                                        ($a->pivot->value ?? ($a->value ?? '—'));
                                                                }
                                                            }
                                                        @endphp
                                                        {!! $attrTexts ? '<div>' . implode('<br>', $attrTexts) . '</div>' : '<span class="small-muted">—</span>' !!}
                                                    </td>
                                                    <td>{{ $variant->price ? number_format($variant->price, 2) : '—' }}</td>
                                                    <td>{{ $variant->sale_price ? number_format($variant->sale_price, 2) : '—' }}
                                                    </td>
                                                    <td>{{ $variant->stock_quantity ?? '—' }}</td>
                                                    <td>{{ $variant->sku ?? '—' }}</td>
                                                    <td>{{ $variant->slug ?? '—' }}</td>
                                                    <td>
                                                        @if (!empty($variant->main_image))
                                                            <img src="{{ asset($variant->main_image) }}" alt="vimg"
                                                                style="width:70px;height:70px;object-fit:cover;">
                                                        @else
                                                            <span class="small-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $variant->is_default ? 'Yes' : 'No' }}</td>
                                                    <td>
                                                        {{-- {{ route('shop.products.variants.show', [$product->id, $variant->id] ?? '#') }} --}}
                                                        <a href="" class="btn btn-sm btn-outline-primary">View</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="small-muted">No variants / combinations found.</div>
                            @endif
                        </div>
                    </div>

                </div>

                <div class="card-footer d-flex align-items-center">
                    <a href="{{ route('shop.products.index') }}" class="btn btn-secondary"><i
                            class="bi bi-arrow-left"></i> Back</a>

                    <div class="ms-auto">
                        <a href="{{ route('shop.products.edit', $product->id) }}" class="btn btn-primary"><i
                                class="bi bi-pencil-square"></i> Edit</a>
                        <form action="{{ route('shop.products.destroy', $product->id) }}" method="POST"
                            class="d-inline-block" onsubmit="return confirm('Delete this product?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger ms-2"><i class="bi bi-trash"></i>
                                Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            {{-- /.card --}}
        </div>
    </div>
@endsection

@push('backend_scripts')
    <script>
        // Initialize tooltips (bootstrap)
        document.addEventListener('DOMContentLoaded', function() {
            var tipList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tipList.map(function(el) {
                return new bootstrap.Tooltip(el);
            });
        });
    </script>
@endpush
