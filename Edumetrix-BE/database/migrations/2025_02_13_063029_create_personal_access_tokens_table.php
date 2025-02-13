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
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable'); // 多態關聯
            $table->string('name'); // Token 名稱
            $table->string('token', 128)->unique(); // Token 值（加密後）
            $table->json('abilities')->nullable(); // Token 權限（JSON 格式）
            $table->timestamp('last_used_at')->nullable(); // 最後使用時間
            $table->timestamp('expires_at')->nullable(); // 過期時間
            $table->timestamps(); // 建立與更新時間
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
