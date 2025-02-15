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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            
            // 用於串接金流: e.g. ECPay
            $table->string('order_number', 50)->unique();
            $table->decimal('total_amount', 8, 2)->default(0);
            $table->enum('status', ['pending','paid','canceled','refunded'])->default('pending');
            $table->string('payment_method', 50)->nullable();  // e.g. credit_card, ecpay
            $table->string('trade_no', 50)->nullable();        // 第三方交易編號

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};