{{-- resources/views/admin/shop_payments/create.blade.php --}}
@extends('layouts.backend.app')

@section('title', 'Add Shop Payment')

@section('backend_content')
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6"><h3 class="mb-0">Add Shop Payment</h3></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item"><a href="">Shop Payments</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Payment</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">Create Shop Payment</h3>
                    </div>

                    <form action="{{ route('admin.shop.subscription.store') }}" method="POST" novalidate>
                        @csrf
                        <div class="card-body">
                            <input type="hidden" name="shop_slug" value="{{ $slug }}" readonly>
                           

                            {{-- Payment Method --}}
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                                <input type="text" name="payment_method" id="payment_method"
                                    class="form-control @error('payment_method') is-invalid @enderror"
                                    value="{{ old('payment_method') }}" placeholder="e.g., Card, Paypal, Bkash">
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Payment Amount & Currency --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="amount" class="form-label">Payment Amount <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="amount" id="amount"
                                        class="form-control @error('amount') is-invalid @enderror"
                                        value="{{ old('amount') }}" placeholder="0.00">
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
                                    <input type="text" name="currency" id="currency"
                                        class="form-control @error('currency') is-invalid @enderror"
                                        value="{{ old('currency', 'USD') }}" placeholder="USD">
                                    @error('currency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Start Date --}}
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="start_date"
                                    class="form-control @error('start_date') is-invalid @enderror"
                                    value="{{ old('start_date') }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Duration Days --}}
                            <div class="mb-3">
                                <label for="duration_days" class="form-label">Duration (Days) <span class="text-danger">*</span></label>
                                <input type="number" name="duration_days" id="duration_days"
                                    class="form-control @error('duration_days') is-invalid @enderror"
                                    value="{{ old('duration_days', 30) }}" min="1" placeholder="Enter duration in days">
                                @error('duration_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- End Date (Auto-calculated) --}}
                            <div class="mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="text" id="end_date" class="form-control" readonly placeholder="Auto-calculated">
                            </div>

                        </div>

                        <div class="card-footer d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.shops.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Save Payment
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@push('backend_scripts')
<script>
    // Auto-calculate end date based on start date + duration
    const startInput = document.getElementById('start_date');
    const durationInput = document.getElementById('duration_days');
    const endInput = document.getElementById('end_date');

    function calculateEndDate() {
        const startDate = new Date(startInput.value);
        const duration = parseInt(durationInput.value);

        if (!isNaN(startDate.getTime()) && !isNaN(duration)) {
            const endDate = new Date(startDate);
            endDate.setDate(endDate.getDate() + duration);
            endInput.value = endDate.toISOString().split('T')[0];
        } else {
            endInput.value = '';
        }
    }

    startInput.addEventListener('change', calculateEndDate);
    durationInput.addEventListener('input', calculateEndDate);
</script>
@endpush
