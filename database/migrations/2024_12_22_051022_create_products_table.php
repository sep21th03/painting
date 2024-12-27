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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên sản phẩm
            $table->text('info'); // Thông tin sản phẩm
            $table->text('description'); // Mô tả sản phẩm
            $table->integer('discount')->default(0); 
            $table->foreignId('set_category_id')->constrained('setcategories')->onDelete('cascade'); // Kết nối với bảng setcategories
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
