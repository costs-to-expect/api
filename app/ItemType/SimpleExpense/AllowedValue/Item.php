<?php
declare(strict_types=1);

namespace App\ItemType\SimpleExpense\AllowedValue;

use App\HttpResponse\Responses;
use App\ItemType\AllowedValue;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Subcategory;

class Item extends AllowedValue
{
    public function __construct(
        int $resource_type_id,
        int $resource_id,
        array $viewable_resource_types
    )
    {
        parent::__construct(
            $resource_type_id,
            $resource_id,
            $viewable_resource_types
        );

        $this->setAllowedValueFields();
    }

    public function fetch(): AllowedValue
    {
        $this->fetchValuesForCategory();

        $this->fetchValuesForSubcategory();

        $this->fetchValuesForCurrency();

        return $this;
    }

    protected function setAllowedValueFields(): void
    {
        $this->values = [
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
                    'description' => trans('item-type-simple-expense/allowed-values.description-prefix-category') .
                        $category['category_name'] .
                        trans('item-type-simple-expense/allowed-values.description-suffix-category')
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
                    'description' => trans('item-type-simple-expense/allowed-values.description-prefix-subcategory') .
                        $subcategory['subcategory_name'] . trans('item-type-simple-expense/allowed-values.description-suffix-subcategory')
                ];
            }

            $this->values['subcategory'] = ['allowed_values' => $allowed_values];
        }
    }
}
