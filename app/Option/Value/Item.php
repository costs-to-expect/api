<?php
declare(strict_types=1);

namespace App\Option\Value;

class Item extends Value
{
    /**
     *
     * @param integer $resource_id
     *
     * @return array
     */
    public function allowedValuesForYear(int $resource_id): array
    {
        return [];
    }

    /**
     *
     * @return array
     */
    public function allowedValuesForMonth(): array
    {
        return [];
    }

    /**
     *
     * @return array
     */
    public function allowedValuesForCategory(
        int $resource_type_id,
        array $permitted_resource_types,
        bool $include_public
    ): array
    {
        return [];
    }

    /**
     *
     * @return array
     */
    public function allowedValuesForSubcategory(
        int $resource_type_id,
        int $category_id
    ): array
    {
        return [];
    }
}
