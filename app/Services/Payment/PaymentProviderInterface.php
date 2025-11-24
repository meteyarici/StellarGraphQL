<?php

namespace App\Services\Payment;

interface PaymentProviderInterface
{
    /**
     * Ödeme işlemini gerçekleştir
     *
     * @param float $amount
     * @param array $metadata
     * @return array
     */
    public function pay(float $amount, array $metadata = []): array;
}
