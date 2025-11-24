<?php

namespace App\GraphQL\Mutations;

use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;


class PurchaseMutation extends BaseMutation
{
    public function buy($root, array $args, $context)
    {

        $user = $context->user();
        $productId = $args['productId'];
        $quantity = $args['quantity'] ?? 1;
        $idempotencyKey = $args['idempotencyKey'];
        $redisKey = "idemp:buy:$idempotencyKey";

        if ($this->redis->exists($redisKey)) {

            return [
                'success' => false,
                'message' => 'Duplicate request detected',
                'orderId' => null
            ];
        } else {

            $this->redis->setex($redisKey, 60, 1);
        }

        DB::beginTransaction();
        try {

            $product = Product::lockForUpdate()->find($productId);

            if (!$product || !$product->is_active || $product->stock < $quantity) {
                return [
                    'success' => false,
                    'message' => 'Product not available in requested quantity',
                    'orderId' => null,
                ];
            }

            $softReserveKey = "soft_reserve:product:$productId";
            $reservedQty = $this->redis->get($softReserveKey) ?? 0;

            if ($reservedQty + $quantity > $product->stock) {
                return [
                    'success' => false,
                    'message' => 'Not enough stock available (soft reservation)',
                    'orderId' => null,
                ];
            }

            $this->redis->incrby($softReserveKey, $quantity);
            // reservation süresi (örn. 5 dakika)
            $this->redis->expire($softReserveKey, 300);

            // order oluştur (status: pending)
            $order = Order::create([
                'user_id'    => $user->id,
                'uuid'    => $this->generateUniqueOrderUuid(),
                'product_id' => $productId,
                'quantity'   => $quantity,
                'status'     => 'pending',
                'address_id'     => 1,
                'total_amount'     => 1,
            ]);

            $paymentData = [
                'order_id' => $order->id,
                'user_id'  => $user->id,
                'amount'   => $order->total_price,
                'currency' => 'USD', // ya da product currency
                'payment_method_id' => $args['paymentMethodId'],
                'timestamp'=> now()->timestamp,
            ];

            $this->redis->lpush('payment_queue', json_encode($paymentData));

            // Idempotency sonucu kaydet
            $response = [
                'success' => true,
                'message' => 'Order soft-reserved and queued for payment.',
                'orderId' => $order->id,
            ];

            //$this->redis->setex($idempKey, 60, json_encode($response));
            DB::commit();

            return $response;

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'orderId' => null,
            ];
        }
    }

    /**
     * @return string
     */
    protected function generateUniqueOrderUuid(): string
    {
        do {
            $uuid = (string) \Illuminate\Support\Str::uuid();
            $exists = \App\Models\Order::where('uuid', $uuid)->exists();
        } while ($exists);

        return $uuid;
    }

}
