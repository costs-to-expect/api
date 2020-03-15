<?php
declare(strict_types=1);

namespace App\Interfaces\ResourceTypeItem;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
interface IModel
{
    /**
     * Return an array of the item results based on the requested
     * parameters
     *
     * @param int $resource_type_id
     * @param int $offset
     * @param int $limit
     * @param array $parameters
     * @param array $search_parameters
     * @param array $filter_parameters
     * @param array $sort_parameters
     *
     * @return array
     */
    public function paginatedCollection(
        int $resource_type_id,
        int $offset = 0,
        int $limit = 10,
        array $parameters = [],
        array $search_parameters = [],
        array $filter_parameters = [],
        array $sort_parameters = []
    ): array;

    /**
     * Return the total number of items that match the requested parameters
     *
     * @param int $resource_type_id
     * @param array $parameters
     * @param array $search_parameters
     * @param array $filter_parameters
     *
     * @return int
     */
    public function totalCount(
        int $resource_type_id,
        array $parameters = [],
        array $search_parameters = [],
        array $filter_parameters = []
    ): int;
}
