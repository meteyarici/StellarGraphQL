<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthMutation
{
    public function login($_, array $args)
    {
        $email = $args['email'];
        $password = $args['password'];

        $response = Http::asForm()->post(config('app.url') . '/oauth/token', [
            'grant_type' => 'password',
            'client_id' => env('PASSPORT_PERSONAL_ACCESS_CLIENT_ID'),
            'client_secret' => env('PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET'),
            'username' => $email,
            'password' => $password,
            'scope' => '',
        ]);
        Log::info("OAuth Response", ['body' => $response->body()]);

        if ($response->failed()) {

            Log::info("OAuth Response", ['body' => $response->body()]);

            return [
                'access_token' => null,
                'refresh_token' => null,
                'token_type' => null,
                'expires_in' => null,
                'user' => null
            ];

        }

        $tokens = $response->json();

        return [
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'] ?? null,
            'token_type' => $tokens['token_type'],
            'expires_in' => $tokens['expires_in'],
            'user' => User::where('email', $email)->first()
        ];
    }

    public function me($_, array $args){

        return Auth::user()->load('addresses');
    }
}
