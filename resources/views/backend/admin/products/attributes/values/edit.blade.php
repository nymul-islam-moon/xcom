@extends('layouts.admin.app')

@section('title', 'Edit Attribute Value')

@section('admin_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-sm-6">
                    <h3 class="mb-0">Edit Value for: {{ optional($attributeValue->attribute)->name ?? 'Attribute' }}</h3>
                </div>
                <div class="col-sm-6">
                    <x-admin.breadcrumbs :items="[
                        ['label' => 'Home', 'route' => 'admin.dashboard', 'icon' => 'bi bi-house'],
                        ['label' => 'Product', 'route' => 'admin.products.index'],
                        ['label' => 'Attribute', 'route' => 'admin.products.attributes.index'],
                        ['label' => 'Attribute Values', 'route' => 'admin.products.attributes.show', $attributeValue->attribute_id],
                        ['label' => 'Edit', 'active' => true],
                    ]" />
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Attribute Value</h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.products.attribute-values.update', $attributeValue->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Keep attribute_id but show it readonly so the relation remains clear --}}
                        <div class="mb-3">
                            <label for="attribute_id" class="form-label">Attribute Name</label>
                            <input type="text" id="attribute_id" class="form-control" value="{{ $attributeValue->attribute->name }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="value" class="form-label">Value</label>
                            <input type="text" name="value" id="value" class="form-control @error('value') is-invalid @enderror" value="{{ old('value', $attributeValue->value) }}" placeholder="Enter value (e.g. Red)" required>
                            @error('value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.products.attributes.show', $attributeValue->attribute_id) }}" class="btn btn-secondary">Back</a>
                            <button type="submit" class="btn btn-primary">Update Value</button>
                        </div>
                    </form>
                </div>

                <div class="card-footer text-muted small">
                    Last updated: {{ optional($attributeValue->updated_at)->diffForHumans() ?? '—' }}
                </div>
            </div>
        </div>
    </div>
@endsection