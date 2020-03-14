<?php
declare(strict_types=1);

namespace App\Interfaces\ResourceTypeItem;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
interface ISummaryModel
{
    /**
     * Return the summary for all items for the resources in the requested resource type
     *
     * @param int $resource_type_id
     * @param array $parameters
     *
     * @return array
     */
    public function summary(
        int $resource_type_id,
        array $parameters
    ): array;

    /**
     * Return the summary for all items for the resources in the requested resource
     * type grouped by resource
     *
     * @param int $resource_type_id
     * @param array $parameters
     *
     * @return array
     */
    public function resourcesSummary(
        int $resource_type_id,
        array $parameters
    ): array;

    /**
     * @param int $resource_type_id
     * @param int|null $category_id
     * @param int|null $subcategory_id
     * @param int|null $year
     * @param int|null $month
     * @param array $parameters
     * @param array $search_parameters
     * @return array
     */
    public function filteredSummary(
        int $resource_type_id,
        int $category_id = null,
        int $subcategory_id = null,
        int $year = null,
        int $month = null,
        array $parameters = [],
        array $search_parameters = []
    ): array;
}
