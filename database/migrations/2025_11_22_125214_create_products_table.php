<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')->unique();

            $table->string('title', 255);
            $table->string('slug', 255)->unique();
            $table->string('sku', 150)->unique();
            $table->string('brand', 150)->nullable();

            $table->decimal('price', 10, 2)->default(0);

            $table->integer('stock')->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
