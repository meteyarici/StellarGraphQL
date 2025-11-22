<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('address_id')
                ->constrained('user_addresses')
                ->restrictOnDelete();
            $table->decimal('total_amount', 10, 2);

            /**
             * Sipariş akış durumları
             * pending    → sipariş oluşturuldu ama ödeme alınmadı
             * paid       → ödeme başarılı
             * failed     → ödeme başarısız
             * canceled   → kullanıcı veya sistem tarafından iptal edildi
             *
             * TODO:Enum a eklenmeli
             */
            $table->enum('status', ['pending', 'paid', 'failed', 'canceled'])
                ->default('pending');
            $table->string('payment_transaction_id', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
