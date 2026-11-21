<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->integer('id', true); // INT AUTO_INCREMENT PRIMARY KEY
            $table->integer('product_id');
            $table->integer('customer_id')->nullable();
            $table->integer('sold_by');
            $table->integer('quantity');
            $table->decimal('total_price', 10, 2);
            $table->dateTime('created_at');

            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('sold_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
