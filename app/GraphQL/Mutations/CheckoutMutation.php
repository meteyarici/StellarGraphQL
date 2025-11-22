<?php

namespace App\GraphQL\Mutations;

use App\Jobs\ProcessCheckoutJob;

class CheckoutMutation
{
    public function checkout($_, array $args)
    {
        $input = $args['input'];

        // Kuyruğa ekle
        ProcessCheckoutJob::dispatch([
            'user_id' => $input['userId'],
            'product_id' => $input['productId'],
            'quantity' => $input['quantity'],
            'amount' => $input['amount'],
            'provider' => $input['provider'] ?? 'mock',
        ]);

        return [
            'message' => 'Checkout request received. Payment is being processed.',
        ];
    }
}
