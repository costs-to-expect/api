<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface ItemModel
{
    public function instance(int $item_id): ?Model;

    public function instanceToArray(
        Model $item,
        Model $item_type
    ): array;

    public function paginatedCollection(
        int $resource_type_id,
        int $resource_id,
        int $offset = 0,
        int $limit = 10,
        array $parameters = [],
        array $sort_parameters = [],
        array $search_parameters = [],
        array $filter_parameters = []
    ): array;

    public function single(
        int $resource_type_id,
        int $resource_id,
        int $item_id
    ): ?array;

    public function totalCount(
        int $resource_type_id,
        int $resource_id,
        array $parameters = [],
        array $search_parameters = [],
        array $filter_parameters = []
    ): int;
}
