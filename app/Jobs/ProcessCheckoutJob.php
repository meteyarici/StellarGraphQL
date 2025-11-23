<?php

namespace App\Jobs;

use App\Models\Order;
use App\Payments\PaymentProviderFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Events\OrderStatusUpdated;
use Illuminate\Support\Facades\Log;

class ProcessCheckoutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function handle()
    {
        // Basit: yükü al
        $order = Order::find($this->payload['order_id']);

        if (!$order) {
            Log::error('Order not found in ProcessCheckoutJob', $this->payload);
            return;
        }

        $order->status = 'processing';
        $order->save();

        event(new OrderStatusUpdated($order, 'Processing started'));


        $provider = PaymentProviderFactory::make($this->payload['provider'] ?? 'mock');
        $result = $provider->pay($order->total_price, [
            'order_id' => $order->id,
            'user_id' => $order->user_id,
        ]);

        if (($result['status'] ?? 'failed') === 'success') {
            $order->status = 'completed';
            $order->transaction_id = $result['transaction_id'] ?? null;
            $order->save();

            event(new OrderStatusUpdated($order, 'Payment successful'));
        } else {
            $order->status = 'failed';
            $order->save();

            event(new OrderStatusUpdated($order, 'Payment failed: ' . ($result['message'] ?? '')));
        }
    }
}
