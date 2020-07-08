<?php
declare(strict_types=1);

namespace App\Option\Value;

use App\Item\AbstractItem;
use App\Models\Category;
use App\Models\Subcategory;

class ResourceTypeItem extends Value
{
    /**
     * @param AbstractItem $item_interface
     * @param integer $resource_type_id
     *
     * @return array
     */
    protected function allowedValuesForYear(
        AbstractItem $item_interface,
        int $resource_type_id
    ): array
    {
        $parameters = ['year' => ['allowed_values' => []]];

        return $parameters;
    }

    /**
     * @param AbstractItem $item_interface
     *
     * @return array
     */
    protected function allowedValuesForMonth(
        AbstractItem $item_interface
    ): array
    {
        $parameters = ['month' => ['allowed_values' => []]];

        return $parameters;
    }

    /**
     * @param AbstractItem $item_interface
     * @param integer $resource_type_id
     * @param array $permitted_resource_types
     * @param bool $include_public
     *
     * @return array
     */
    protected function allowedValuesForCategory(
        AbstractItem $item_interface,
        int $resource_type_id,
        array $permitted_resource_types,
        bool $include_public
    ): array
    {
        $parameters = ['category' => ['allowed_values' => []]];

        return $parameters;
    }

    /**
     * @param AbstractItem $item_interface
     * @param integer $resource_type_id
     * @param integer $category_id
     *
     * @return array
     */
    protected function allowedValuesForSubcategory(
        AbstractItem $item_interface,
        int $resource_type_id,
        int $category_id
    ): array
    {
        $parameters = ['subcategory' => ['allowed_values' => []]];

        return $parameters;
    }

    /**
     * @param AbstractItem $item_interface
     * @param integer $resource_type_id
     * @param array $permitted_resource_types
     * @param boolean $include_public
     * @param array $parameters
     *
     * @return array
     */
    public function allowedValues(
        AbstractItem $item_interface,
        int $resource_type_id,
        array $permitted_resource_types,
        bool $include_public,
        array $parameters
    ): array
    {
        $allowed_values = [];

        if (array_key_exists('year', $parameters) === true) {
            array_merge(
                $allowed_values,
                $this->allowedValuesForYear(
                    $item_interface,
                    $resource_type_id
                )
            );
        }

        if (array_key_exists('month', $parameters) === true) {
            array_merge(
                $allowed_values,
                $this->allowedValuesForMonth($item_interface)
            );
        }

        if (array_key_exists('category', $parameters) === true) {
            array_merge(
                $allowed_values,
                $this->allowedValuesForCategory(
                    $item_interface,
                    $resource_type_id,
                    $permitted_resource_types,
                    $include_public
                )
            );
        }

        if (
            array_key_exists('category', $parameters) === true &&
            $parameters['category'] !== null &&
            array_key_exists('subcategory', $parameters) === true
        ) {
            array_merge(
                $parameters,
                $this->allowedValuesForSubcategory(
                    $item_interface,
                    $resource_type_id,
                    $parameters['category']
                )
            );
        }

        return $allowed_values;
    }
}
