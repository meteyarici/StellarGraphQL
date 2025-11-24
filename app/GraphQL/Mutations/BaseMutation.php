<?php

namespace App\GraphQL\Mutations;

use Illuminate\Contracts\Redis\Factory as RedisFactory;

abstract class BaseMutation
{
    protected $redis;

    public function __construct(RedisFactory $redis)
    {
        $this->redis = $redis->connection();
    }

    /**
     * Idempotent wrapper for mutations
     */
    protected function idempotent(string $key, callable $callback, int $ttl = 600)
    {
        $cacheKey = "idempotency:mutation:$key";

        $response = $callback();

        $this->redis->setex($cacheKey, $ttl, json_encode($response));

        return $response;
    }

    /**
     * Extract idempotency key from request header
     */
    protected function getIdempotencyKey($context)
    {
        return $context->request->header('Idempotency-Key');
    }
}
