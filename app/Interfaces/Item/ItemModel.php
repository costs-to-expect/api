<?php
declare(strict_types=1);

namespace App\Interfaces\Item;

use Illuminate\Database\Eloquent\Model;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
interface ItemModel
{
    public function hasCategoryAssignments(int $item_id): bool;

    /**
     * Return an instance of the relevant item model
     *
     * @param int $item_id
     *
     * @return Model|null
     */
    public function instance(int $item_id): ?Model;

    /**
     * Take the Item and ItemType model and return a formatted array for
     * output
     *
     * @param Model $item
     * @param Model $item_type
     *
     * @return array
     */
    public function instanceToArray(
        Model $item,
        Model $item_type
    ): array;

    /**
     * Return an array of the item results based on the requested
     * parameters
     *
     * @param int $resource_type_id
     * @param int $resource_id
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
        int $resource_id,
        int $offset = 0,
        int $limit = 10,
        array $parameters = [],
        array $search_parameters = [],
        array $filter_parameters = [],
        array $sort_parameters = []
    ): array;

    /**
     * Return a single item as an array
     *
     * @param int $resource_type_id
     * @param int $resource_id
     * @param int $item_id
     * @param array $parameters
     *
     * @return array|null
     */
    public function single(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        array $parameters = []
    ): ?array;

    /**
     * Return the total number of items that match the requested parameters
     *
     * @param int $resource_type_id
     * @param int $resource_id
     * @param array $parameters
     * @param array $search_parameters
     * @param array $filter_parameters
     *
     * @return int
     */
    public function totalCount(
        int $resource_type_id,
        int $resource_id,
        array $parameters = [],
        array $search_parameters = [],
        array $filter_parameters = []
    ): int;
}
