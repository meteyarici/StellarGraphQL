<?php

namespace App\GraphQL\Queries;

use App\Models\Product;

class ProductQuery
{
    /**
     * Meilisearch üzerinden arama
     */
    public function search($root, array $args)
    {
        $query = $args['query'] ?? '';
        $brand = $args['brand'] ?? null;
        $minPrice = $args['minPrice'] ?? null;
        $maxPrice = $args['maxPrice'] ?? null;
        $inStock = $args['inStock'] ?? null;
        $page = $args['page'] ?? 1;
        $perPage = $args['perPage'] ?? 20;

        // Scout Builder
        $builder = Product::search($query);

        // Filtreleri ayrı ayrı ekliyoruz
        if ($brand) {
            $builder->where('brand', $brand);
        }

        if ($minPrice !== null) {
            $builder->where('price', '>=', $minPrice);
        }

        if ($maxPrice !== null) {
            $builder->where('price', '<=', $maxPrice);
        }

        if ($inStock !== null) {
            if ($inStock) {
                $builder->where('stock', 1); // 1 veya 0 kullan, >0 Scout ile çalışmaz
            } else {
                $builder->where('stock', 0);
            }
        }

        // Scout paginate
        $results = $builder->paginate($perPage, 'page', $page);

        return [
            'data' => $results->items(),
            'current_page' => $results->currentPage(),
            'last_page' => $results->lastPage(),
            'total' => $results->total(),
        ];
    }

    /**
     * Normal pagination (GraphQL products sorgusu için)
     */
    public function __invoke($_, array $args)
    {
        $page = $args['page'] ?? 1;
        $perPage = $args['perPage'] ?? 20;

        return Product::paginate(
            $perPage,
            ['*'],
            'page',
            $page
        );
    }

    public function products($_, array $args)
    {
        $page = $args['page'] ?? 1;
        $perPage = $args['perPage'] ?? 20;

        $results = Product::paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $results->items(),
            'paginatorInfo' => [
                'count' => $results->count(),
                'currentPage' => $results->currentPage(),
                'firstItem' => $results->firstItem(),
                'hasMorePages' => $results->hasMorePages(),
                'lastItem' => $results->lastItem(),
                'lastPage' => $results->lastPage(),
                'perPage' => $results->perPage(),
                'total' => $results->total(),
            ],
        ];
    }
}
