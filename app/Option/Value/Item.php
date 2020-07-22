<?php
declare(strict_types=1);

namespace App\Option\Value;

use App\Item\AbstractItem;
use App\Models\Category;
use App\Models\Subcategory;

class Item extends Value
{
    /**
     * @param AbstractItem $item_interface
     * @param integer $resource_id
     *
     * @return array
     */
    protected function allowedValuesForYear(
        AbstractItem $item_interface,
        int $resource_id
    ): array
    {
        $parameters = ['year' => ['allowed_values' => []]];

        for (
            $i = $item_interface->conditionalParameterMinYear($resource_id);
            $i <= $item_interface->conditionalParameterMaxYear($resource_id);
            $i++
        ) {
            $parameters['year']['allowed_values'][$i] = [
                'value' => $i,
                'name' => $i,
                'description' => trans('item-type-' . $item_interface->type() .
                        '/allowed-values.description-prefix-year') . $i
            ];
        }

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

        for ($i=1; $i < 13; $i++) {
            $parameters['month']['allowed_values'][$i] = [
                'value' => $i,
                'name' => date("F", mktime(0, 0, 0, $i, 10)),
                'description' => trans('item-type-' . $item_interface->type() .
                        '/allowed-values.description-prefix-month') .
                    date("F", mktime(0, 0, 0, $i, 1))
            ];
        }

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

        $categories = (new Category())->paginatedCollection(
            $resource_type_id,
            $permitted_resource_types,
            $include_public,
            0,
            100
        );

        foreach ($categories as $category) {
            $category_id = $this->hash->encode('category', $category['category_id']);

            $parameters['category']['allowed_values'][$category_id] = [
                'value' => $category_id,
                'name' => $category['category_name'],
                'description' => trans('item-type-' . $item_interface->type() .
                        '/allowed-values.description-prefix-category') .
                    $category['category_name'] .
                    trans('item-type-' . $item_interface->type() .
                        '/allowed-values.description-suffix-category')
            ];
        }

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

        $subcategories = (new Subcategory())->paginatedCollection(
            $resource_type_id,
            $category_id
        );

        array_map(
            function($subcategory) use (&$parameters, $item_interface) {
                $subcategory_id = $this->hash->encode('subcategory', $subcategory['subcategory_id']);
                $parameters['subcategory']['allowed_values'][$subcategory_id] = [
                    'value' => $subcategory_id,
                    'name' => $subcategory['subcategory_name'],
                    'description' => trans('item-type-' . $item_interface->type() . '/allowed-values.description-prefix-subcategory') .
                        $subcategory['subcategory_name'] . trans('item-type-' . $item_interface->type() . '/allowed-values.description-suffix-subcategory')
                ];
            },
            $subcategories
        );

        return $parameters;
    }

    /**
     * @param AbstractItem $item_interface
     * @param integer $resource_type_id
     * @param integer $resource_id
     * @param array $permitted_resource_types
     * @param boolean $include_public
     * @param array $parameters
     *
     * @return array
     */
    public function allowedValues(
        AbstractItem $item_interface,
        int $resource_type_id,
        int $resource_id,
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
                    $resource_id
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
