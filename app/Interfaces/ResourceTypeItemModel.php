<?php
declare(strict_types=1);

namespace App\Interfaces;

interface ResourceTypeItemModel
{
    public function paginatedCollection(
        int $resource_type_id,
        int $offset = 0,
        int $limit = 10,
        array $parameters = [],
        array $sort_parameters = [],
        array $search_parameters = [],
        array $filter_parameters = []
    ): array;

    public function totalCount(
        int $resource_type_id,
        array $parameters = [],
        array $search_parameters = [],
        array $filter_parameters = []
    ): int;
}
