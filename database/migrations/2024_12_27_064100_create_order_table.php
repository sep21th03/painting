<?php

use App\Models\Order;
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
            $table->string('code')->unique(); 
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('total_price');
            $table->text('note')->nullable(); 
            $table->string('user_name'); 
            $table->string('phone'); 
            $table->string('address'); 
            $table->unsignedSmallInteger('payment_method'); 
            $table->tinyInteger('status')->default(Order::STATUS_WAITING); 
            $table->text('message')->nullable(); 
            $table->decimal('shipping_fee', 10, 2)->default(0); 
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
