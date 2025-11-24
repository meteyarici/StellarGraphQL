<?php

namespace App\GraphQL\Queries;

use App\Models\User;

class UserQuery
{
    // Meilisearch arama
    public function search($root, array $args)
    {
        $query = $args['query'] ?? '';
        $email = $args['email'] ?? null;
        $status = $args['status'] ?? null;
        $page = $args['page'] ?? 1;
        $perPage = $args['perPage'] ?? 20;

        $builder = User::search($query);

        $filters = [];
        if ($email) {
            $filters[] = "email = \"$email\"";
        }
        if ($status) {
            $filters[] = "status = \"$status\"";
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

        $query = User::query();

        return [
            'data' => $query->paginate($perPage, ['*'], 'page', $page)->items(),
            'paginatorInfo' => [
                'count' => $query->count(),
                'currentPage' => $page,
                'lastPage' => ceil($query->count() / $perPage),
                'perPage' => $perPage,
                'total' => $query->count(),
                'hasMorePages' => $page < ceil($query->count() / $perPage)
            ]
        ];
    }
}
