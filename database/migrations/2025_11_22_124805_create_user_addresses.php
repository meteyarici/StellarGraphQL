<?php

use     Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('title', 100)->nullable();
            $table->string('full_name', 150);
            $table->string('country', 100)->default('Turkey');
            $table->string('city', 100);
            $table->string('district', 100);
            $table->string('address', 500);

            $table->string('postal_code', 20)->nullable();
            $table->string('phone', 30)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
