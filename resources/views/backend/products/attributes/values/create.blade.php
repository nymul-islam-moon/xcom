@extends('layouts.admin.app')

@section('title', 'Add Attribute Value')

@section('admin_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-sm-6">
                    <h3 class="mb-0">Add Value to: {{ $attribute->name }}</h3>
                </div>
                {{-- <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="">Attributes</a></li>
                        <li class="breadcrumb-item active">Add Value</li>
                    </ol>
                </div> --}}
                <div class="col-sm-6">
                    <x-admin.breadcrumbs :items="[
                        ['label' => 'Home', 'route' => 'admin.dashboard', 'icon' => 'bi bi-house'],
                        ['label' => 'Product', 'route' => 'admin.products.index'],
                        ['label' => 'Attribute', 'route' => 'admin.products.attributes.index'],
                        ['label' => 'Attribute Values', 'route' => 'admin.products.attributes.show', $attribute->id],
                        ['label' => 'Create', 'active' => true],
                    ]" />
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">New Attribute Value</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.products.attribute-values.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="attribute_id" value="{{ $attribute->id }}">

                        <div class="mb-3">
                            <label for="value" class="form-label">Value</label>
                            <input type="text" name="value" id="value" class="form-control" placeholder="Enter value (e.g. Red)" required>
                            @error('value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.products.attributes.show', $attribute->id) }}" class="btn btn-secondary">Back</a>
                            <button type="submit" class="btn btn-success">Add Value</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
