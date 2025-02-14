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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');                   // 課程名稱
            $table->text('description')->nullable();    // 課程描述
            $table->string('cover_image')->nullable();  // 課程封面圖片
            $table->unsignedBigInteger('teacher_id');   // 講師ID
            $table->unsignedBigInteger('category_id')->nullable();    // 大分類ID
            $table->unsignedBigInteger('subcategory_id')->nullable(); // 小分類ID
            $table->decimal('rating', 2, 1)->default(0);       // 評價星數 (例如 4.5)
            $table->unsignedBigInteger('rating_count')->default(0);   // 評價人數
            $table->unsignedBigInteger('purchase_count')->default(0); // 購買人數
            $table->decimal('price', 8, 2)->default(0);        // 課程價格
            $table->timestamps();

            // Foreign Keys
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('subcategory_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
