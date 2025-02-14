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
            $table->string('name', 100)->nullable(); // 用戶名稱
            $table->string('email', 191)->unique();  // 用戶 Email
            $table->string('password');
            $table->string('provider', 50)->nullable(); // google, facebook, etc.
            $table->string('provider_id', 100)->nullable();
            $table->rememberToken(); // Laravel remember_token，用於 "記住我" 功能
            $table->timestamps();
            // $table->softDeletes(); // 可選的軟刪除欄位
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
