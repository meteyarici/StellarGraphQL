<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Typesense\Client;
use Typesense\Exceptions\TypesenseClientError;

class CreateTypesenseCollections extends Command
{
    protected $signature = 'typesense:create-collections';
    protected $description = 'Typesense collections for models';

    public function handle()
    {



        $client = new Client([
            'api_key' => env('TYPESENSE_API_KEY', 'xyz'),
            'nodes' => [
                [
                    'host' => env('TYPESENSE_HOST', 'shop-typesense'),
                    'port' => env('TYPESENSE_PORT', '8108'),
                    'protocol' => env('TYPESENSE_PROTOCOL', 'http'),
                ],
            ],
            'connection_timeout_seconds' => 2,
        ]);

        try {
            $client->collections->create([
                'name' => 'products',
                'fields' => [
                    ['name' => 'id', 'type' => 'int32'],
                    ['name' => 'title', 'type' => 'string'],
                    ['name' => 'description', 'type' => 'string'],
                    ['name' => 'brand', 'type' => 'string', 'facet' => true],
                    ['name' => 'stock', 'type' => 'int32'],
                    ['name' => 'price', 'type' => 'float'],
                ],
                'default_sorting_field' => 'price',
            ]);
            $this->info('Products collection created successfully.');
        } catch (TypesenseClientError $e) {
            $this->error($e->getMessage());
        }
    }
}
