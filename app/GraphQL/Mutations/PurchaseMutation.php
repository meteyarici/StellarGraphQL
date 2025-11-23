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


        return [
            'success' => false,
            'message' => 'next' . $idempotencyKey . ' '  ,
            'orderId' => null,
        ];
        die('key yok');



        $user = $context->user();



        // 2️⃣ Distributed Lock — eş zamanlı satın almaları engelle
        $lock = $redis->set($lockKey, 1, 'NX', 'EX', 5);
        if (!$lock) {
            return [
                'success' => false,
                'message' => 'Product is currently being processed. Try again.',
                'orderId' => null,
            ];
        }

        try {
            DB::beginTransaction();

            $product = Product::lockForUpdate()->find($productId);

            if (!$product || !$product->is_active || $product->stock < $quantity) {
                $response = [
                    'success' => false,
                    'message' => 'Product not available in requested quantity',
                    'orderId' => null,
                ];
                $redis->setex($idempKey, 60, json_encode($response));
                DB::rollBack();
                return $response;
            }

            // 3️⃣ Stok güncelle
            $product->stock -= $quantity;
            $product->save();

            // 4️⃣ Siparişi oluştur
            $order = Order::create([
                'user_id'          => $user->id,
                'product_id'       => $product->id,
                'quantity'         => $quantity,
                'total_price'      => $product->price * $quantity,
                'payment_method_id'=> $args['paymentMethodId'],
                'status'           => 'paid',
            ]);

            DB::commit();

            $response = [
                'success' => true,
                'message' => 'Purchase successful',
                'orderId' => $order->id,
            ];

            // 5️⃣ Idempotency sonucunu kaydet (60 saniye)
            $redis->setex($idempKey, 60, json_encode($response));

            return $response;

        } catch (\Exception $e) {
            DB::rollBack();

            $response = [
                'success' => false,
                'message' => $e->getMessage(),
                'orderId' => null,
            ];

            $redis->setex($idempKey, 60, json_encode($response));

            return $response;

        } finally {
            // 6️⃣ Lock'u bırak
            $redis->del($lockKey);
        }
    }

}
