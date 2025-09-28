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
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 15)->nullable();
            $table->string('slug')->unique();
            $table->string('shop_keeper_name');
            $table->string('shop_keeper_phone');
            $table->string('shop_keeper_nid');
            $table->string('shop_keeper_email')->nullable();
            $table->string('shop_keeper_photo')->nullable();
            $table->string('shop_keeper_tin');
            $table->string('dbid')->nullable()->comment('Digital Business Identification');
            $table->string('bank_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->enum('status', [ 'pending', 'active', 'inactive', 'suspended' ])->default('pending');
            $table->string('bank_account_number')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('shop_logo')->nullable();
            $table->text('description')->nullable();
            $table->text('business_address')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
