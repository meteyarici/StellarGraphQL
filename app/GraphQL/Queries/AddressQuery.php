<?php

namespace App\GraphQL\Queries;

use App\Models\Address;

class AddressQuery
{
    // Meilisearch arama
    public function search($root, array $args)
    {
        $query = $args['query'] ?? '';
        $city = $args['city'] ?? null;
        $country = $args['country'] ?? null;
        $page = $args['page'] ?? 1;
        $perPage = $args['perPage'] ?? 20;

        $builder = Address::search($query);

        $filters = [];
        if ($city) {
            $filters[] = "city = \"$city\"";
        }
        if ($country) {
            $filters[] = "country = \"$country\"";
        }

        if (!empty($filters)) {
            $builder->where(implode(' AND ', $filters));
        }

        $results = $builder->paginate($perPage, 'page', $page);

        return [
            'data' => $results->items(),
            'current_page' => $results->currentPage(),
            'last_page' => $results->lastPage(),
            'total' => $results->total(),
        ];
    }

    // Normal DB paginate
    public function list($_, array $args)
    {
        $page = $args['page'] ?? 1;
        $perPage = $args['perPage'] ?? 20;

        return Address::paginate($perPage, ['*'], 'page', $page);
    }
}
