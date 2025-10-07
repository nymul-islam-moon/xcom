<?php

namespace App\Http\Requests\Backend\Admin;

use App\Models\Shop;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;


class StoreShopPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected $stopOnFirstFailure = true;

    public function attributes(): array
    {
        return [
            'payment_method'        => 'payment method',
            'amount'                => 'payment amount',
            'currency'              => 'payment currency',
            'start_date'            => 'subscription start date',
            'duration_days'         => 'subscription duration days',
            'end_date'              => 'subscription end date',
            'transaction_number'    => 'payment transaction number',
        ];
    }


    protected function prepareForValidation()
    {
        $startDate = $this->input('start_date');
        $durationDays = (int) $this->input('duration_days');

        // Calculate end date only if start date and duration are valid
        $endDate = null;
        if ($startDate && $durationDays > 0) {
            $endDate = Carbon::parse($startDate)->addDays($durationDays)->format('Y-m-d');
        }

        $shopId = Shop::where('slug', request('shop_slug'))->value('id');

        // Always merge payment_date and normalize currency
        $this->merge([
            'shop_id'       => $shopId,
            'end_date'      => $endDate,
            'payment_date'  => Carbon::now()->format('Y-m-d'),
            'currency'      => strtoupper(trim($this->input('currency', 'USD'))), // default USD if empty
        ]);
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'shop_id'               => ['required', 'integer'],
            'payment_method'        => ['required', 'string', 'max:255'],
            'transaction_number'    => ['nullable', 'string', 'max:255'],
            'payment_date'          => ['required', 'date'],
            'amount'                => ['required', 'numeric', 'min:0'],
            'currency'              => ['required', 'string', 'max:10'],
            'start_date'            => ['required', 'date', 'after_or_equal:today'],
            'duration_days'         => ['required', 'integer', 'min:1'],
            'end_date'              => ['required', 'date', 'after:start_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'shop_id.required'          => 'Please select a shop.',
            'shop_id.exists'            => 'The selected shop does not exist.',
            'payment_method.required'   => 'Please enter a payment method.',
            'payment_method.string'     => 'Payment method must be a valid string.',
            'payment_method.max'        => 'Payment method cannot exceed 255 characters.',
            'amount.required'           => 'Please enter the payment amount.',
            'amount.numeric'            => 'Payment amount must be a number.',
            'amount.min'                => 'Payment amount must be at least 0.',
            'currency.required'         => 'Please enter the currency.',
            'currency.string'           => 'Currency must be a valid string.',
            'currency.max'              => 'Currency cannot exceed 10 characters.',
            'start_date.required'       => 'Please select a start date.',
            'start_date.date'           => 'Start date must be a valid date.',
            'start_date.after_or_equal' => 'Start date cannot be in the past.',
            'duration_days.required'    => 'Please enter the subscription duration in days.',
            'duration_days.integer'     => 'Duration must be an integer.',
            'duration_days.min'         => 'Duration must be at least 1 day.',
            'end_date.required'         => 'End date could not be calculated.',
            'end_date.date'             => 'End date must be a valid date.',
            'end_date.after'            => 'End date must be after the start date.',
        ];
    }


    /**
     * Handle failed validation.
     */
    protected function failedValidation(Validator $validator)
    {
        // Log all errors
        Log::error('Shop Subscription validation failed', [
            'errors' => $validator->errors()->toArray(),
            'input'  => $this->all(),
        ]);

        throw new HttpResponseException(
            redirect()->back()
                ->withErrors($validator)
                ->withInput()
        );
    }
}
