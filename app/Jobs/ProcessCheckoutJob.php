<?php

namespace App\Jobs;

use App\Services\Payment\PaymentProviderFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCheckoutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $checkoutData;

    public function __construct(array $checkoutData)
    {
        $this->checkoutData = $checkoutData;
    }

    public function handle()
    {
        $amount = $this->checkoutData['amount'];
        $provider = $this->checkoutData['provider'] ?? 'mock';

        $paymentProvider = PaymentProviderFactory::make($provider);
        $result = $paymentProvider->pay($amount, $this->checkoutData);

        // Ödeme başarılıysa siparişi tamamla
        if ($result['status'] === 'success') {
            // Örn: Siparişi DB'ye kaydet
        }

        return $result;
    }
}
