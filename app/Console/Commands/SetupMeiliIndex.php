<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MeiliSearch\Client;
use MeiliSearch\Exceptions\ApiException;

class SetupMeiliIndex extends Command
{
    protected $signature = 'meili:setup';
    protected $description = 'Meilisearch index oluşturur ve filterable attribute ekler';

    public function handle()
    {
        $host = config('scout.meilisearch.host');
        $key = config('scout.meilisearch.key');
        $indexName = 'products';

        $this->info("Meilisearch client oluşturuluyor...");

        $client = new Client($host, $key);

        try {
            $client->getIndex($indexName);
            $this->info("Index '$indexName' zaten mevcut.");
        } catch (ApiException $e) {

            $client->createIndex($indexName);
            $this->info("Index '$indexName' oluşturuldu.");
        }

        $client->index($indexName)->updateFilterableAttributes([
            'name',
            'brand',
            'stock',
            'price',
            'in_stock',
        ]);

        $this->info("Filterable attributes ayarlandı: brand, price, stock, price, in_stock ");
        $this->info("Meilisearch index setup tamamlandı ✅");
    }
}
