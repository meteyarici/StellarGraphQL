<?php

namespace App\Services\Payment;

use App\Services\Payment\Providers\DefaultPaymentProvider;
use App\Services\Payment\Decorators\PaymentLoggerDecorator;

class PaymentProviderFactory
{
    public static function create(string $provider = null): PaymentProviderInterface
    {
        switch ($provider) {
            case 'stripe':
                // $instance = new StripePaymentProvider();
                break;
            case 'paypal':
                // $instance = new PayPalPaymentProvider();
                break;
            default:
                $instance = new DefaultPaymentProvider();
        }

        // Otomatik olarak decorator ile sarmala
        return new PaymentLoggerDecorator($instance);
    }
}
