{{-- resources/views/admin/attribute-values/show.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Attribute Value Details')

@section('admin_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Attribute Value Details</h3>
                </div>
                <div class="col-sm-6">
                    <x-admin.breadcrumbs :items="[
                        ['label' => 'Home', 'route' => 'admin.dashboard', 'icon' => 'bi bi-house'],
                        ['label' => 'Product', 'route' => 'admin.products.index'],
                        ['label' => 'Attribute', 'route' => 'admin.products.attributes.index'],
                        [
                            'label' => 'Attribute Values',
                            'route' => 'admin.products.attributes.show',
                            $attributeValue->attribute_id,
                        ],
                        ['label' => 'Details', 'active' => true],
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
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title mb-0">Value — {{ $attributeValue->value }}</h3>

                            <div class="ms-auto d-flex align-items-center gap-2">
                                <a href="{{ route('admin.products.attribute-values.edit', $attributeValue) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form action="{{ route('admin.products.attribute-values.destroy', $attributeValue) }}"
                                    method="POST" class="m-0 p-0"
                                    onsubmit="return confirm('Delete this attribute value?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i>
                                        Delete</button>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            <dl class="row mb-0">
                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3 border-bottom">ID</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 border-bottom mb-0 fw-semibold text-break">
                                    {{ $attributeValue->id }}</dd>

                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3 border-bottom">Attribute
                                    ID</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 border-bottom mb-0 text-break">
                                    {{ $attributeValue->attribute_id }}</dd>

                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3 border-bottom">Attribute
                                    Name</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 border-bottom mb-0 text-break">
                                    {{ optional($attributeValue->attribute)->name ?? '— (attribute deleted) —' }}</dd>

                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3 border-bottom">Value</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 border-bottom mb-0 text-break">
                                    {{ $attributeValue->value }}</dd>

                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3 border-bottom">Created
                                </dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 border-bottom mb-0">
                                    {{ optional($attributeValue->created_at)->format('M d, Y h:i A') }}</dd>

                                <dt class="col-6 col-sm-3 text-start text-muted small py-2 py-sm-3">Last Updated</dt>
                                <dd class="col-6 col-sm-9 text-end py-2 py-sm-3 mb-0">
                                    {{ optional($attributeValue->updated_at)->format('M d, Y h:i A') }}</dd>
                            </dl>
                        </div>

                        <div class="card-footer d-flex align-items-center">
                            <a href="{{ route('admin.products.attributes.show', $attributeValue->attribute_id) }}"
                                class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Attribute
                            </a>

                            <div class="ms-auto d-flex align-items-center gap-2 flex-nowrap text-nowrap">
                                <a href="{{ route('admin.products.attributes.show', $attributeValue->attribute_id) }}" class="btn btn-primary">All
                                    Attribute Values</a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
