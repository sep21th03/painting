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
        Schema::create('product_sizes', function (Blueprint $table) {
            $table->id();
            $table->string('size'); // Kích thước (ví dụ: S, M, L)
            $table->decimal('price', 8, 2); // Giá của sản phẩm
            $table->integer('stock')->default(0); // Số lượng tồn kho
            $table->foreignId('product_hex_id')->constrained('product_hex')->onDelete('cascade'); // Kết nối với bảng product_hex
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_sizes');
    }
};
