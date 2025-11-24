<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use App\Models\Order;

class ProcessPaymentQueue extends Command
{
    protected $signature = 'queue:worker-payment';
    protected $description = 'Process soft reserved orders for payment';

    public function handle()
    {
        $this->info('Payment worker started...');

        while (true) {
            $data = Redis::lpop('payment_queue');

            if (!$data) {
                sleep(1); // Kuyruk boşsa bekle
                continue;
            }

            $item = json_decode($data, true);
            $order = Order::find($item['order_id']);
            if (!$order || $order->status !== 'reserved') {
                continue; // geçersiz veya işlenmiş
            }

            try {
                // --- Ödeme işleme mantığı ---
                // $paymentResult = PaymentProvider::charge(...);
                $paymentResult = true; // Örnek: başarılı

                if ($paymentResult) {
                    $order->status = 'paid';
                    $order->save();

                    // Burada istersen GraphQL subscription ile frontend’e yay
                    // event(new OrderPaid($order));

                    // ödeme kuyruğuna ekle
                    /*$redis->xadd('payments_stream', '*', [
                        'order_id' => $order->id,
                        'user_id' => $user->id,
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'idempotency_key' => $idempotencyKey,
                    ]);*/

                } else {
                    $order->status = 'failed';
                    $order->save();
                }

            } catch (\Exception $e) {
                $this->error("Payment failed for order {$order->id}: {$e->getMessage()}");
                // Hata durumunda tekrar kuyruğa ekleyebilirsin
                Redis::rpush('payment_queue', $data);
            }
        }
    }
}
