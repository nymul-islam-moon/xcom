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
        Schema::create('shop_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->nullable()->constrained('shops')->onDelete('cascade');

            // Payment info
            $table->string('payment_method'); // e.g., 'card', 'paypal'
            $table->string('transaction_id')->nullable();
            $table->date('payment_date'); // when payment was made

            // Activation/subscription info
            $table->date('start_date')->nullable(); // null until activated
            $table->integer('duration_days'); // number of active days
            $table->date('end_date')->nullable(); // calculated on approval

            $table->enum('status', ['pending', 'active', 'expired'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_payments');
    }
};
