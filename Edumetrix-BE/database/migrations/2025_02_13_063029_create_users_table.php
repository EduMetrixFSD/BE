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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // 用戶名稱
            $table->string('email')->unique(); // 用戶 Email（唯一）
            $table->string('password'); // 密碼
            $table->string('provider')->nullable(); // 第三方供應商（如 Google）
            $table->string('provider_id')->nullable(); // 第三方用戶 ID
            $table->timestamps(); // 建立與更新時間
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
