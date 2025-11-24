<?php

use App\Models\Product;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Search Engine
    |--------------------------------------------------------------------------
    |
    | This option controls the default search connection that gets used while
    | using Laravel Scout. This connection is used when syncing all models
    | to the search service. You should adjust this based on your needs.
    |
    | Supported: "algolia", "meilisearch", "typesense", "database", "collection", "null", "elastic"
    |
    */

    'driver' => env('SCOUT_DRIVER', 'meilisearch'),


    /*
    |--------------------------------------------------------------------------
    | Index Prefix
    |--------------------------------------------------------------------------
    |
    | Here you may specify a prefix that will be applied to all search index
    | names used by Scout. Useful if you have multiple tenants/applications
    | sharing the same search infrastructure.
    |
    */

    'prefix' => env('SCOUT_PREFIX', ''),

    /*
    |--------------------------------------------------------------------------
    | Queue Data Syncing
    |--------------------------------------------------------------------------
    |
    | If true, all automatic data syncing will be queued for better performance.
    |
    */

    'queue' => env('SCOUT_QUEUE', false),

    /*
    |--------------------------------------------------------------------------
    | Database Transactions
    |--------------------------------------------------------------------------
    |
    | If true, data will only be synced after database transaction commits.
    |
    */

    'after_commit' => true,

    /*
    |--------------------------------------------------------------------------
    | Chunk Sizes
    |--------------------------------------------------------------------------
    |
    | Maximum chunk size when mass importing data into the search engine.
    |
    */

    'chunk' => [
        'searchable' => 500,
        'unsearchable' => 500,
    ],

    /*
    |--------------------------------------------------------------------------
    | Soft Deletes
    |--------------------------------------------------------------------------
    |
    | Whether to keep soft deleted records in the search indexes.
    |
    */

    'soft_delete' => false,

    /*
    |--------------------------------------------------------------------------
    | Elasticsearch Configuration
    |--------------------------------------------------------------------------
    */

    'typesense' => [
        'client-settings' => [   // <-- BU OLMALI
            'api_key' => env('TYPESENSE_API_KEY', 'xyz'),
            'nodes' => [
                [
                    'host' => env('TYPESENSE_HOST', '127.0.0.1'),
                    'port' => env('TYPESENSE_PORT', '8108'),
                    'protocol' => env('TYPESENSE_PROTOCOL', 'http'),
                ],
            ],
            'connection_timeout_seconds' => 2,
        ],
        'max_total_results' => 1000,
        'collection-settings' => [
            Product::class => [
                'fields' => [
                    ['name' => 'id', 'type' => 'int32'],
                    ['name' => 'title', 'type' => 'string'],
                    ['name' => 'description', 'type' => 'string'],
                    ['name' => 'brand', 'type' => 'string', 'facet' => true],
                    ['name' => 'stock', 'type' => 'int32'],
                    ['name' => 'price', 'type' => 'float'],
                ],
                'default_sorting_field' => 'price',
            ],
        ],
    ],




];
