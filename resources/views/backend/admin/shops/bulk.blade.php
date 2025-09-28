{{-- resources/views/admin/shops/bulk.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Bulk Upload Shops')

@section('admin_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">Bulk Upload Shops</h3></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.shops.index') }}">Shops</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Bulk Upload</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-8">

                    <div class="card mb-4">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title mb-0">Bulk Upload (CSV / Excel)</h3>
                        </div>

                        @php($bulkErrors = $errors->getBag('bulkUpload'))
                        <form action="{{ route('admin.bulkUpload.shop.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                            @csrf
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="bulk_file" class="form-label">Upload file <span class="text-danger">*</span></label>
                                    <input
                                        type="file"
                                        id="bulk_file"
                                        name="bulk_file"
                                        class="form-control @if($bulkErrors->has('bulk_file')) is-invalid @endif @error('bulk_file') is-invalid @enderror"
                                        accept=".csv, .xls, .xlsx">

                                    {{-- Support both: named bag and default bag --}}
                                    @if($bulkErrors->has('bulk_file'))
                                        <div class="invalid-feedback">{{ $bulkErrors->first('bulk_file') }}</div>
                                    @elseif($errors->has('bulk_file'))
                                        <div class="invalid-feedback">{{ $errors->first('bulk_file') }}</div>
                                    @endif

                                    <div class="form-text">
                                        Accepted: .csv, .xls, .xlsx. Columns should match: name, email, phone,
                                        shop_keeper_name, shop_keeper_phone, shop_keeper_nid, shop_keeper_tin, dbid,
                                        bank_name, bank_account_number, bank_branch, website_url, description,
                                        business_address, email_verified_at (Y-m-d H:i), password.
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <a href="" class="btn btn-outline-secondary">
                                        <i class="bi bi-download"></i> Download Sample
                                    </a>
                                    <button type="submit" class="btn btn-primary ms-auto">
                                        <i class="bi bi-upload"></i> Import
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div> <!-- /.col -->
            </div> <!-- /.row -->
        </div> <!-- /.container-fluid -->
    </div> <!-- /.app-content -->
@endsection
