<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Support\Str;

class OrdersTableSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $address = UserAddress::where('user_id', $user->id)->first();

        Order::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $user->id,
            'address_id' => $address->id,
            'total_amount' => 220,
            'status' => 'pending',
            'payment_transaction_id' => null
        ]);
    }
}
