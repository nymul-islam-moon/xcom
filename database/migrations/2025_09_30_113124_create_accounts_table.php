<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();

            // Optional direct owner lookups (keep for fast queries; not tied to any specific domain model)
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('owner_type')->nullable(); // optional polymorphic owner (e.g., 'App\Models\Shop', 'App\Models\User')

            // Generic polymorphic reference to domain objects (orders, subscriptions, payouts, etc.)
            // This is the main link to any model and avoids hardcoding model names/ids.
            $table->nullableMorphs('reference'); // reference_type, reference_id

            // Classification
            $table->string('category')->default('payment'); // payment, refund, payout, commission, fee, adjustment...
            $table->string('sub_type')->nullable(); // optional more specific tag, e.g., 'subscription', 'product_sale'

            // Ledger semantics
            $table->enum('direction', ['credit','debit'])->default('credit'); // credit = money in, debit = money out
            $table->decimal('amount', 16, 2)->default(0);
            $table->string('currency', 3)->default('USD');

            // Provider/payment details (fully generic)
            $table->string('provider')->nullable(); // e.g., 'stripe','paypal','bkash','bank','manual'
            $table->string('transaction_id')->nullable(); // provider transaction id (nullable for manual entries)
            $table->string('payment_method')->nullable(); // e.g., 'card','bank_transfer','cash_on_delivery'

            // Status & timestamps
            $table->enum('status', ['pending','completed','failed','refunded','cancelled'])->default('pending');
            $table->timestamp('happened_at')->nullable();   // when the movement occurred
            $table->timestamp('reconciled_at')->nullable(); // when reconciled by accounting

            // Extensible metadata
            $table->json('meta')->nullable(); // store raw gateway payload, notes, reasons, etc.
            $table->string('reference_code')->nullable(); // optional human/internal reference code
            $table->uuid('uuid')->nullable()->unique(); // optional external UUID

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
