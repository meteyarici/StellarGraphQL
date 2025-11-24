<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class OrderItemsTableSeeder extends Seeder
{
    public function run(): void
    {
        $order = Order::first();

        $product = Product::first();

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->title,
            'unit_price' => $product->price,
            'quantity' => 2,
            'total_price' => $product->price * 2
        ]);
    }
}
