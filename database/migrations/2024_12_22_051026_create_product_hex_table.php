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
        Schema::create('product_hex', function (Blueprint $table) {
            $table->id();
            $table->string('hex_code'); // Mã sản phẩm (ví dụ: R300, R400)
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Kết nối với bảng products
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_hex');
    }
};
