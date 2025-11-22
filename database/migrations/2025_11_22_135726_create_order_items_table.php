<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->string('product_name');                   // O anki ürün adı
            $table->decimal('unit_price', 18, 2);             // O anki ürün fiyatı
            $table->integer('quantity');                      // Miktar
            $table->decimal('total_price', 18, 2);            // quantity * unit_price

            // Varyant desteği gerekiyorsa:
            // $table->unsignedBigInteger('variant_id')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

