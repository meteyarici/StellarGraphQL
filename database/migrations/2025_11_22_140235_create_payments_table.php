<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Hangi siparişe ait?
            $table->unsignedBigInteger('order_id')->index();

            // Ödeme sağlayıcısı bilgisi (iyzico, stripe, paypal, etc.)
            $table->string('provider')->nullable();

            // Ödeme yöntemi (credit_card, wallet, bank_transfer, etc.)
            $table->string('method')->nullable();

            // Ödeme tutarı ve para birimi
            $table->decimal('amount', 18, 2);
            $table->string('currency', 3)->default('TRY');

            // Payment provider tarafından dönen işlem ID’si
            $table->string('transaction_id')->nullable()->index();

            // Ödeme durumu
            // pending, paid, failed, refunded, canceled...
            $table->enum('status', [
                'pending',
                'paid',
                'failed',
                'refunded',
                'canceled'
            ])->default('pending');

            // Provider raw response (log)
            $table->json('payload')->nullable();

            // Timestamps
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

