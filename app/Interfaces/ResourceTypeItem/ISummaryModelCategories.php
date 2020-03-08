<?php
declare(strict_types=1);

namespace App\Interfaces\ResourceTypeItem;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
interface ISummaryModelCategories
{
    /**
     * Return the summary for all items for the resources in the requested resource
     * type grouped by category
     *
     * @param integer $resource_type_id
     * @param array $parameters
     *
     * @return array
     */
    public function categoriesSummary(
        int $resource_type_id,
        array $parameters
    ): array;

    /**
     * Return the summary for all items for the resources in the requested resource
     * type for the requested category
     *
     * @param integer $resource_type_id
     * @param integer $category_id
     * @param array $parameters
     *
     * @return array
     */
    public function categorySummary(
        int $resource_type_id,
        int $category_id,
        array $parameters
    ): array;

    /**
     * Return the summary for all items for the resources in the requested resource
     * type and category grouped by subcategory
     *
     * @param int $resource_type_id
     * @param int $category_id
     * @param array $parameters
     *
     * @return array
     */
    public function subcategoriesSummary(
        int $resource_type_id,
        int $category_id,
        array $parameters
    ): array;

    /**
     * Return the summary for all items for the resources in the requested resource
     * type and category and subcategory
     *
     * @param int $resource_type_id
     * @param int $category_id
     * @param int $subcategory_id
     * @param array $parameters
     *
     * @return array
     */
    public function subcategorySummary(
        int $resource_type_id,
        int $category_id,
        int $subcategory_id,
        array $parameters
    ): array;
}
