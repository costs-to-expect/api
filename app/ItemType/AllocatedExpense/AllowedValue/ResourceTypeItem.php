<?php
declare(strict_types=1);

namespace App\ItemType\AllocatedExpense\AllowedValue;

use App\ItemType\ResourceTypeAllowedValue;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Subcategory;
use App\Response\Responses;

class ResourceTypeItem extends ResourceTypeAllowedValue
{
    public function __construct(
        int $resource_type_id,
        array $viewable_resource_types
    )
    {
        parent::__construct(
            $resource_type_id,
            $viewable_resource_types
        );

        $this->setAllowedValueFields();
    }

    public function fetch(): ResourceTypeAllowedValue
    {
        $this->fetchValuesForYear();

        $this->fetchValuesForMonth();

        $this->fetchValuesForCategory();

        $this->fetchValuesForSubcategory();

        $this->fetchValuesForCurrency();

        return $this;
    }

    protected function setAllowedValueFields(): void
    {
        $this->values = [
            'year' => null,
            'month' => null,
            'category' => null,
            'subcategory' => null,
            'currency_id' => null
        ];
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
                    'description' => trans('resource-type-item-type-allocated-expense/allowed-values.description-prefix-category') .
                        $category['category_name'] .
                        trans('resource-type-item-type-allocated-expense/allowed-values.description-suffix-category')
                ];
            }

            $this->values['category'] = ['allowed_values' => $allowed_values];
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

    protected function fetchValuesForMonth(): void
    {
        if (array_key_exists('month', $this->available_parameters) === true) {

            $allowed_values = [];

            for ($i = 1; $i < 13; $i++) {
                $allowed_values[$i] = [
                    'value' => $i,
                    'name' => date("F", mktime(0, 0, 0, $i, 10)),
                    'description' => trans('resource-type-item-type-allocated-expense/allowed-values.description-prefix-month') .
                        date("F", mktime(0, 0, 0, $i, 1))
                ];
            }

            $this->values['month'] = ['allowed_values' => $allowed_values];
        }
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
                    'description' => trans('resource-type-item-type-allocated-expense/allowed-values.description-prefix-subcategory') .
                        $subcategory['subcategory_name'] . trans('resource-type-item-type-allocated-expense/allowed-values.description-suffix-subcategory')
                ];
            }

            $this->values['subcategory'] = ['allowed_values' => $allowed_values];
        }
    }

    protected function fetchValuesForYear(): void
    {
        if (array_key_exists('year', $this->available_parameters) === true) {

            $allowed_values = [];

            $min_year = $this->range_limits->minimumYearByResourceType(
                $this->resource_type_id,
                'item_type_allocated_expense',
                'effective_date'
            );
            $max_year = $this->range_limits->maximumYearByResourceType(
                $this->resource_type_id,
                'item_type_allocated_expense',
                'effective_date'
            );

            for ($i = $min_year; $i <= $max_year; $i++) {
                $allowed_values[$i] = [
                    'value' => $i,
                    'name' => $i,
                    'description' => trans('resource-type-item-type-allocated-expense/allowed-values.description-prefix-year') . $i
                ];
            }

            $this->values['year'] = ['allowed_values' => $allowed_values];
        }
    }
}
