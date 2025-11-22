<?php

namespace App\Services\Payment\Providers;

use App\Services\Payment\PaymentProviderInterface;

class DefaultPaymentProvider implements PaymentProviderInterface
{
    public function pay(float $amount, array $metadata = []): array
    {
        return [
            'status' => 'success',
            'transaction_id' => 'TXN_' . rand(100000, 999999),
            'amount' => $amount,
            'metadata' => $metadata,
        ];
    }
}
