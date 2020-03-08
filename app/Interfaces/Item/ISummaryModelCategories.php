<?php
declare(strict_types=1);

namespace App\Interfaces\Item;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
interface ISummaryModelCategories
{
    /**
     * Return the summary of items, grouped by category
     *
     * @param int $resource_type_id
     * @param int $resource_id
     * @param array $parameters
     *
     * @return array
     */
    public function categoriesSummary(
        int $resource_type_id,
        int $resource_id,
        array $parameters = []
    ): array;

    /**
     * Return the summary for a specific category
     *
     * @param int $resource_type_id
     * @param int $resource_id
     * @param int $category_id
     * @param array $parameters
     *
     * @return array
     */
    public function categorySummary(
        int $resource_type_id,
        int $resource_id,
        int $category_id,
        array $parameters = []
    ): array;

    /**
     * Subcategories summary
     *
     * @param int $resource_type_id
     * @param int $resource_id
     * @param int $category_id
     * @param array $parameters
     *
     * @return array
     */
    public function subcategoriesSummary(
        int $resource_type_id,
        int $resource_id,
        int $category_id,
        array $parameters = []
    ): array;

    /**
     * Subcategory summary
     *
     * @param int $resource_type_id
     * @param int $resource_id
     * @param int $category_id
     * @param int $subcategory_id
     * @param array $parameters
     *
     * @return array
     */
    public function subcategorySummary(
        int $resource_type_id,
        int $resource_id,
        int $category_id,
        int $subcategory_id,
        array $parameters = []
    ): array;
}
