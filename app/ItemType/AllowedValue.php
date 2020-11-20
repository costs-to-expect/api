<?php
declare(strict_types=1);

namespace App\ItemType;

use App\Models\Category;
use App\Models\Currency;
use App\Models\EntityLimits;
use App\Models\Subcategory;
use App\Request\Hash;
use App\Response\Responses;

abstract class AllowedValue
{
    protected Hash $hash;

    protected \App\ItemType\ItemType $entity;

    protected EntityLimits $range_limits;

    protected int $resource_type_id;
    protected int $resource_id;

    protected array $viewable_resource_types;

    protected array $available_parameters = [];
    protected array $defined_parameters = [];

    protected array $values = [];

    public function __construct(
        int $resource_type_id,
        int $resource_id,
        array $viewable_resource_types
    )
    {
        $this->resource_type_id = $resource_type_id;
        $this->resource_id = $resource_id;

        $this->viewable_resource_types = $viewable_resource_types;

        $this->range_limits = new EntityLimits();

        $this->hash = new Hash();
    }

    public function setParameters(
        array $available_parameters,
        array $defined_parameters
    ): AllowedValue
    {
        $this->available_parameters = $available_parameters;
        $this->defined_parameters = $defined_parameters;

        return $this;
    }

    abstract public function fetch(): AllowedValue;

    abstract protected function setAllowedValueFields(): void;

    public function allowedValues(): array
    {
        foreach ($this->values as $field => $value) {
            if ($value === null) {
                unset($this->values[$field]);
            }
        }

        return $this->values;
    }

    protected function fetchValuesForCategory(): void
    {
        if (array_key_exists('category', $this->available_parameters) === true) {

            $allowed_values = [];

            $categories = (new Category())->paginatedCollection(
                $this->resource_type_id,
                $this->viewable_resource_types,
                0,
                100
            );

            foreach ($categories as $category) {
                $category_id = $this->hash->encode('category', $category['category_id']);

                $allowed_values[$category_id] = [
                    'value' => $category_id,
                    'name' => $category['category_name'],
                    'description' => trans('item-type-' . $this->entity->type() .
                            '/allowed-values.description-prefix-category') .
                        $category['category_name'] .
                        trans('item-type-' . $this->entity->type() .
                            '/allowed-values.description-suffix-category')
                ];
            }

            $this->values['category'] = ['allowed_values' => $allowed_values];
        }
    }

    protected function fetchValuesForMonth(): void
    {
        if (array_key_exists('month', $this->available_parameters) === true) {

            $allowed_values = [];

            for ($i = 1; $i < 13; $i++) {
                $allowed_values[$i] = [
                    'value' => $i,
                    'name' => date("F", mktime(0, 0, 0, $i, 10)),
                    'description' => trans('item-type-' . $this->entity->type() .
                            '/allowed-values.description-prefix-month') .
                        date("F", mktime(0, 0, 0, $i, 1))
                ];
            }

            $this->values['month'] = ['allowed_values' => $allowed_values];
        }
    }

    protected function fetchValuesForCurrency(): void
    {
        $allowed_values = [];

        $currencies = (new Currency())->minimisedCollection();

        foreach ($currencies as $currency) {
            $id = $this->hash->encode('currency', $currency['currency_id']);

            if ($id === false) {
                Responses::unableToDecode();
            }

            $allowed_values[$id] = [
                'value' => $id,
                'name' => $currency['currency_name'],
                'description' => $currency['currency_name']
            ];
        }

        $this->values['currency_id'] = ['allowed_values' => $allowed_values];
    }

    protected function fetchValuesForSubcategory(): void
    {
        if (
            array_key_exists('category', $this->available_parameters) === true &&
            array_key_exists('subcategory', $this->available_parameters) === true &&
            array_key_exists('category', $this->defined_parameters) === true &&
            $this->defined_parameters['category'] !== null
        ) {

            $allowed_values = [];

            $subcategories = (new Subcategory())->paginatedCollection(
                $this->resource_type_id,
                (int) $this->defined_parameters['category']
            );

            foreach ($subcategories as $subcategory) {
                $subcategory_id = $this->hash->encode('subcategory', $subcategory['subcategory_id']);

                $allowed_values[$subcategory_id] = [
                    'value' => $subcategory_id,
                    'name' => $subcategory['subcategory_name'],
                    'description' => trans('item-type-' . $this->entity->type() . '/allowed-values.description-prefix-subcategory') .
                        $subcategory['subcategory_name'] . trans('item-type-' . $this->entity->type() . '/allowed-values.description-suffix-subcategory')
                ];
            }

            $this->values['subcategory'] = ['allowed_values' => $allowed_values];
        }
    }

    protected function fetchValuesForYear(): void
    {
        if (array_key_exists('year', $this->available_parameters) === true) {

            $allowed_values = [];

            for (
                $i = $this->range_limits->minimumYearByResourceTypeAndResource(
                    $this->resource_type_id,
                    $this->resource_id,
                    $this->entity->table(),
                    $this->entity->dateRangeField()
                );
                $i <= $this->range_limits->maximumYearByResourceTypeAndResource(
                    $this->resource_type_id,
                    $this->resource_id,
                    $this->entity->table(),
                    $this->entity->dateRangeField()
                );
                $i++
            ) {
                $allowed_values[$i] = [
                    'value' => $i,
                    'name' => $i,
                    'description' => trans('item-type-' . $this->entity->type() .
                            '/allowed-values.description-prefix-year') . $i
                ];
            }

            $this->values['year'] = ['allowed_values' => $allowed_values];
        }
    }
}
