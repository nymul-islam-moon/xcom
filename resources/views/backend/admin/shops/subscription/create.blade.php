{{-- resources/views/admin/shop_payments/create.blade.php --}}
@extends('layouts.backend.app')

@section('title', 'Add Shop Payment')

@section('backend_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Add Shop Payment</h3>
                </div>
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
                        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                            <h3 class="card-title mb-0">Create Shop Payment – {{ $shop->name }}</h3>
                        </div>


                        <form action="{{ route('admin.shop-subscription.store', $shop->slug) }}" method="POST" novalidate>
                            @csrf
                            <div class="card-body">
                                <input type="hidden" name="shop_slug" value="{{ $shop->slug }}" readonly>

                                {{-- Payment Method --}}
                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">Payment Method <span
                                            class="text-danger">*</span></label>
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
                                        <label for="amount" class="form-label">Payment Amount <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.01" name="amount" id="amount"
                                            class="form-control @error('amount') is-invalid @enderror"
                                            value="{{ old('amount') }}" placeholder="0.00">
                                        @error('amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="currency" class="form-label">Currency <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="currency" id="currency"
                                            class="form-control @error('currency') is-invalid @enderror"
                                            value="{{ old('currency', 'BDT') }}" placeholder="BDT">
                                        @error('currency')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Start Date --}}
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Date <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="start_date"
                                        class="form-control @error('start_date') is-invalid @enderror"
                                        value="{{ old('start_date') }}">
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Duration Days --}}
                                <div class="mb-3">
                                    <label for="duration_days" class="form-label">Duration (Days) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="duration_days" id="duration_days"
                                        class="form-control @error('duration_days') is-invalid @enderror"
                                        value="{{ old('duration_days', 30) }}" min="1"
                                        placeholder="Enter duration in days">
                                    @error('duration_days')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- End Date (Auto-calculated) --}}
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="text" id="end_date" class="form-control" readonly
                                        placeholder="Auto-calculated">
                                </div>

                                {{-- Transaction Number --}}
                                <div class="mb-3">
                                    <label for="transaction_number" class="form-label">Transaction Number <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="transaction_number" id="transaction_number"
                                        class="form-control @error('transaction_number') is-invalid @enderror"
                                        value="{{ old('transaction_number') }}" placeholder="e.g., TX123456789">
                                    @error('transaction_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            <div class="card-footer d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.shop-subscription.index', $shop->slug) }}"
                                    class="btn btn-outline-secondary">
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
        $(function() {
            // Laravel variable passed into JS
            let startDateFromPHP = "{{ $startDate ?? '' }}";

            let startDate;
            if (startDateFromPHP) {
                // Add 1 day if date exists
                let dateObj = new Date(startDateFromPHP);
                dateObj.setDate(dateObj.getDate() + 1);
                startDate = dateObj.toISOString().split('T')[0];
            } else {
                // Otherwise use today's date
                startDate = new Date().toISOString().split('T')[0];
            }

            // Set start date
            $('#start_date').val(startDate);

            // Auto-calculate end date
            function calculateEndDate() {
                let startDateVal = $('#start_date').val();
                let duration = parseInt($('#duration_days').val());

                if (startDateVal && !isNaN(duration)) {
                    let endDate = new Date(startDateVal);
                    endDate.setDate(endDate.getDate() + duration);
                    $('#end_date').val(endDate.toISOString().split('T')[0]);
                } else {
                    $('#end_date').val('');
                }
            }

            // Trigger on change
            $('#start_date, #duration_days').on('change keyup', calculateEndDate);

            // Initial calculation
            calculateEndDate();
        });
    </script>
@endpush
