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
            $table->foreignId('shop_id')
                ->nullable()
                ->constrained('shops')
                ->onDelete('cascade');

            // Subscription / plan info
            $table->string('payment_method'); // e.g., 'card', 'paypal'
            $table->date('payment_date'); // date payment was made

            $table->date('start_date')->nullable(); // when subscription activates
            $table->integer('duration_days'); // e.g., 30, 90, etc.
            $table->date('end_date')->nullable(); // auto-calculated

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
