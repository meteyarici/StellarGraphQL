<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
    use App\Models\Order;
use App\Models\Payment;

class PaymentsTableSeeder extends Seeder
{
    public function run(): void
    {
        $order = Order::first();

        Payment::create([
            'order_id' => $order->id,
            'provider' => 'fake_gateway',
            'method' => 'credit_card',
            'amount' => $order->total_amount,
            'currency' => 'TRY',
            'status' => 'pending',
            'payload' => json_encode(['note' => 'Test payment'])
        ]);
    }
}

