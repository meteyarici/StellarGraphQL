<?php

namespace App\GraphQL\Queries;

use App\Models\Product;
use Chr15k\MeilisearchAdvancedQuery\MeilisearchQuery;
use Laravel\Scout\Builder;

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

        $filters = [];
        if (!empty($minPrice)) $filters[] = "price:>={$minPrice}";
        if (!empty($maxPrice)) $filters[] = "price:<={$maxPrice}";
        if (!empty($brand)) $filters[] = "brand:={$brand}";

        $builder = Product::search($query)->options([
            'query_by' => 'title,description,brand',
            'filter_by' => !empty($filters) ? implode(' && ', $filters) : null,
            'per_page' => $perPage,
            'page' => $page,
        ]);

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


    public function detail($_, array $args){
        return Product::find($args['id']);
    }
}
