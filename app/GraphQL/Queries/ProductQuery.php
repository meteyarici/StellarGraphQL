<?php

namespace App\GraphQL\Queries;

use App\Models\Product;

class ProductQuery
{
    /**
     * Meilisearch üzerinden arama
     */
    public function search($_, array $args)
    {
        $query = $args['query'] ?? '';
        $brand = $args['brand'] ?? null;
        $minPrice = $args['minPrice'] ?? null;
        $maxPrice = $args['maxPrice'] ?? null;
        $inStock = $args['inStock'] ?? null;
        $page = $args['page'] ?? 1;
        $perPage = $args['perPage'] ?? 20;

        // Meilisearch arama
        $builder = Product::search($query);

        // Meilisearch filtre dizisi
        $filters = [];

        if ($brand) {
            $filters[] = "brand = \"$brand\"";
        }

        if ($minPrice !== null) {
            $filters[] = "price >= $minPrice";
        }

        if ($maxPrice !== null) {
            $filters[] = "price <= $maxPrice";
        }

        if ($inStock !== null) {
            $filters[] = $inStock ? "stock > 0" : "stock = 0";
        }

        if (!empty($filters)) {
            $builder->where(implode(' AND ', $filters));
        }

        $result = $builder->paginate($perPage, 'page', $page);

        return [
            'data' => $result->items(),
            'current_page' => $result->currentPage(),
            'last_page' => $result->lastPage(),
            'total' => $result->total(),
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
