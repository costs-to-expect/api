<?php
declare(strict_types=1);

namespace App\Option\AllowedValues;

use App\Entity\Item\Item as Entity;
use App\Models\EntityConfig;
use App\Request\Hash;
use App\Models\Category;
use App\Models\Subcategory;

class ResourceTypeItem
{
    private Hash $hash;

    private Entity $entity;

    private EntityConfig $model;

    public function __construct(Entity $entity)
    {
        $this->hash = new Hash();

        $this->entity = $entity;

        $this->model = new EntityConfig();
    }

    protected function allowedValuesForYear(int $resource_type_id): array
    {
        $parameters = ['year' => ['allowed_values' => []]];

        for (
            $i = $this->model->minimumYearByResourceType(
                $resource_type_id,
                $this->entity->table(),
                $this->entity->dateRangeField()
            );
            $i <= $this->model->maximumYearByResourceType(
                $resource_type_id,
                $this->entity->table(),
                $this->entity->dateRangeField()
            );
            $i++
        ) {
            $parameters['year']['allowed_values'][$i] = [
                'value' => $i,
                'name' => $i,
                'description' => trans('resource-type-item-type-' . $this->entity->type() .
                        '/allowed-values.description-prefix-year') . $i
            ];
        }

        return $parameters;
    }

    protected function allowedValuesForMonth(): array
    {
        $parameters = ['month' => ['allowed_values' => []]];

        for ($i=1; $i < 13; $i++) {
            $parameters['month']['allowed_values'][$i] = [
                'value' => $i,
                'name' => date("F", mktime(0, 0, 0, $i, 10)),
                'description' => trans('resource-type-item-type-' . $this->entity->type() .
                        '/allowed-values.description-prefix-month') .
                    date("F", mktime(0, 0, 0, $i, 1))
            ];
        }

        return $parameters;
    }

    protected function allowedValuesForCategory(
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
                'description' => trans('resource-type-item-type-' . $this->entity->type() .
                        '/allowed-values.description-prefix-category') .
                    $category['category_name'] .
                    trans('resource-type-item-type-' . $this->entity->type() .
                        '/allowed-values.description-suffix-category')
            ];
        }

        return $parameters;
    }

    protected function allowedValuesForSubcategory(
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
            function($subcategory) {
                $subcategory_id = $this->hash->encode('subcategory', $subcategory['subcategory_id']);

                $parameters['subcategory']['allowed_values'][$subcategory_id] = [
                    'value' => $subcategory_id,
                    'name' => $subcategory['subcategory_name'],
                    'description' => trans('resource-type-item-type-' . $this->entity->type() . '/allowed-values.description-prefix-subcategory') .
                        $subcategory['subcategory_name'] . trans('resource-type-item-type-' . $this->entity->type() . '/allowed-values.description-suffix-subcategory')
                ];
            },
            $subcategories
        );

        return $parameters;
    }

    public function allowedValues(
        int $resource_type_id,
        array $permitted_resource_types,
        bool $include_public,
        array $available_parameters,
        array $defined_parameters
    ): array
    {
        $years = [];
        if (array_key_exists('year', $available_parameters) === true) {
            $years = $this->allowedValuesForYear($resource_type_id);
        }

        $months = [];
        if (array_key_exists('month', $available_parameters) === true) {
            $months = $this->allowedValuesForMonth();
        }

        $categories = [];
       if (array_key_exists('category', $available_parameters) === true) {
            $categories = $this->allowedValuesForCategory(
                $resource_type_id,
                $permitted_resource_types,
                $include_public
            );
        }

        $subcategories = [];
        if (
            array_key_exists('category', $available_parameters) === true &&
            array_key_exists('subcategory', $available_parameters) === true &&
            array_key_exists('category', $defined_parameters) === true &&
            $defined_parameters['category'] !== null
        ) {
            $subcategories = $this->allowedValuesForSubcategory(
                $resource_type_id,
                $defined_parameters['category']
            );
        }

        return array_merge(
            $years,
            $months,
            $categories,
            $subcategories
        );
    }
}
