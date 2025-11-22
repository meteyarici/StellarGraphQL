<?php

namespace App\Services\Payment\Decorators;

use App\Services\Payment\PaymentProviderInterface;
use Illuminate\Support\Facades\Log;

class PaymentLoggerDecorator implements PaymentProviderInterface
{
    private PaymentProviderInterface $provider;

    public function __construct(PaymentProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    public function pay(float $amount, array $metadata = []): array
    {
        $result = $this->provider->pay($amount, $metadata);

        // Loglama
        Log::info('Payment processed', [
            'provider' => get_class($this->provider),
            'amount' => $amount,
            'metadata' => $metadata,
            'result' => $result,
        ]);

        return $result;
    }
}
