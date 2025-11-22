<?php

namespace App\GraphQL\Mutations;

use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PurchaseMutation
{
    public function buy($root, array $args, $context)
    {
        $user = $context->user(); // Authenticated user
        $product = Product::find($args['productId']);
        $quantity = $args['quantity'] ?? 1;

        if (!$product || !$product->is_active || $product->stock < $quantity) {
            return [
                'success' => false,
                'message' => 'Product not available in requested quantity',
                'orderId' => null,
            ];
        }

        DB::beginTransaction();
        try {
            // Stok güncelle
            $product->stock -= $quantity;
            $product->save();

            // Order oluştur
            $order = Order::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'total_price' => $product->price * $quantity,
                'payment_method_id' => $args['paymentMethodId'],
                'status' => 'paid', // Basit senaryo
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Purchase successful',
                'orderId' => $order->id,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'orderId' => null,
            ];
        }
    }
}
