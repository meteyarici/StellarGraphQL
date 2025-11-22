<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserAddress;

class UserAddressesTableSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            UserAddress::create([
                'user_id' => $user->id,
                'title' => 'Home Address',
                'full_name' => $user->name,
                'country' => 'Turkey',
                'city' => 'Istanbul',
                'district' => 'Kadikoy',
                'address' => 'Example Street 123',
                'postal_code' => '34710',
                'phone' => '+905555555555'
            ]);
        }
    }
}

