<?php

declare(strict_types=1);

namespace App\ItemType\AllocatedExpense;

use App\HttpRequest\Hash;
use App\HttpRequest\Parameter\Request;
use App\HttpResponse\Response;
use App\Models\Category;
use App\Models\EntityLimits;
use App\Models\Subcategory;
use Illuminate\Support\Facades\Config;
use JetBrains\PhpStorm\ArrayShape;

class AllowedValue
{
    protected Hash $hash;
    protected int $resource_type_id;
    protected ?int $resource_id;
    protected array $viewable_resource_types;

    public function __construct(
        array $viewable_resource_types,
        int $resource_type_id,
        ?int $resource_id = null
    ) {
        $this->hash = new Hash();

        $this->resource_type_id = $resource_type_id;
        $this->resource_id = $resource_id;
        $this->viewable_resource_types = $viewable_resource_types;
    }

    #[ArrayShape([
        'category' => "array[]",
        'subcategory' => "array[]",
        'year' => "array[]",
        'month' => "array[]",
    ])]
    public function parameterAllowedValuesForCollection(): array
    {
        if ($this->resource_id === null) {
            throw new \InvalidArgumentException("Resource id needs to be defined in the constructor for a collection");
        }

        $parameters = Config::get('api.item-type-allocated-expense.parameters', []);
        $parameters_set_in_request = Request::fetch(
            array_keys($parameters),
            $this->resource_type_id,
            $this->resource_id
        );

        return [
            'category' => ['allowed_values' => $this->assignAllowedValuesForCategory()],
            'subcategory' => ['allowed_values' => $this->assignAllowedValuesForSubcategory($parameters_set_in_request)],
            'year' => ['allowed_values' => $this->assignAllowedValuesForYear()],
            'month' => ['allowed_values' => $this->assignAllowedValuesForMonth($parameters_set_in_request)],
        ];
    }

    #[ArrayShape([
        'category' => "array[]",
        'subcategory' => "array[]",
        'year' => "array[]",
        'month' => "array[]",
    ])]
    public function parameterAllowedValuesForResourceTypeCollection(): array
    {
        if ($this->resource_id !== null) {
            throw new \InvalidArgumentException("Resource id does not need to be defined in the constructor for a resoure type collection");
        }

        $parameters = Config::get('api.resource-type-item-type-allocated-expense.parameters', []);
        $parameters_set_in_request = Request::fetch(
            array_keys($parameters),
            $this->resource_type_id
        );

        return [
            'category' => ['allowed_values' => $this->assignAllowedValuesForCategory()],
            'subcategory' => ['allowed_values' => $this->assignAllowedValuesForSubcategory($parameters_set_in_request)],
            'year' => ['allowed_values' => $this->assignAllowedValuesForYearAndResourceType()],
            'month' => ['allowed_values' => $this->assignAllowedValuesForMonth($parameters_set_in_request)],
        ];
    }

    #[ArrayShape(['currency_id' => "array[]"])]
    public function fieldAllowedValuesForCollection(): array
    {
        return [
            'currency_id' => ['allowed_values' => $this->assignAllowedValuesForCurrency()]
        ];
    }

    #[ArrayShape(['currency_id' => "array[]"])]
    public function fieldAllowedValuesForShow(): array
    {
        return [
            'currency_id' => ['allowed_values' => $this->assignAllowedValuesForCurrency()]
        ];
    }

    private function assignAllowedValuesForCategory(): array
    {
        $allowed_values = [];

        $categories = (new Category())->paginatedCollection(
            $this->resource_type_id,
            $this->viewable_resource_types,
            0,
            100
        );

        foreach ($categories as $category) {
            $category_id = $this->hash->encode('category', $category['category_id']);

            if ($category_id === false) {
                Response::unableToDecode();
            }

            $allowed_values[$category_id] = [
                'uri' => route('category.show', ['resource_type_id' => $this->resource_type_id, 'category_id' => $category_id], false),
                'value' => $category_id,
                'name' => $category['category_name'],
                'description' => trans('item-type-allocated-expense/allowed-values.description-prefix-category') .
                    $category['category_name'] .
                    trans('item-type-allocated-expense/allowed-values.description-suffix-category')
            ];
        }

        return $allowed_values;
    }

    private function assignAllowedValuesForCurrency(): array
    {
        $allowed_values = [];

        $currencies = (new \App\Models\Currency())->minimisedCollection();

        foreach ($currencies as $currency) {
            $id = $this->hash->encode('currency', $currency['currency_id']);

            if ($id === false) {
                Response::unableToDecode();
            }

            $allowed_values[$id] = [
                'uri' => route('currency.show', ['currency_id' => $id], false),
                'value' => $id,
                'name' => $currency['currency_name'],
                'description' => $currency['currency_name']
            ];
        }

        return $allowed_values;
    }

    private function assignAllowedValuesForMonth(
        array $parameters_set_in_request
    ): array {
        $allowed_values = [];

        for ($i = 1; $i < 13; $i++) {
            $allowed_values[$i] = [
                'value' => $i,
                'name' => date("F", mktime(0, 0, 0, $i, 10)),
                'description' => trans('item-type-allocated-expense/allowed-values.description-prefix-month') .
                    date("F", mktime(0, 0, 0, $i, 1))
            ];
        }

        return $allowed_values;
    }

    private function assignAllowedValuesForYear(): array
    {
        $allowed_values = [];

        $entity_limits = (new EntityLimits());

        $min_year = $entity_limits->minimumYearByResourceTypeAndResource(
            $this->resource_type_id,
            $this->resource_id,
            'item_type_allocated_expense',
            'effective_date'
        );
        $max_year = $entity_limits->maximumYearByResourceTypeAndResource(
            $this->resource_type_id,
            $this->resource_id,
            'item_type_allocated_expense',
            'effective_date'
        );

        for ($i = $min_year; $i <= $max_year; $i++) {
            $allowed_values[$i] = [
                'value' => $i,
                'name' => $i,
                'description' => trans('item-type-allocated-expense/allowed-values.description-prefix-year') . $i
            ];
        }

        return $allowed_values;
    }

    private function assignAllowedValuesForYearAndResourceType(): array
    {
        $allowed_values = [];

        $entity_limits = (new EntityLimits());

        $min_year = $entity_limits->minimumYearByResourceType(
            $this->resource_type_id,
            'item_type_allocated_expense',
            'effective_date'
        );
        $max_year = $entity_limits->maximumYearByResourceType(
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

        return $allowed_values;
    }

    private function assignAllowedValuesForSubcategory(
        array $parameters_set_in_request
    ): array {
        $allowed_values = [];

        if (
            array_key_exists('category', $parameters_set_in_request) === true &&
            $parameters_set_in_request['category'] !== null
        ) {
            $subcategories = (new Subcategory())->paginatedCollection(
                $this->resource_type_id,
                (int) $parameters_set_in_request['category']
            );

            $category_id = $this->hash->encode('category', $parameters_set_in_request['category']);

            foreach ($subcategories as $subcategory) {
                $subcategory_id = $this->hash->encode('subcategory', $subcategory['subcategory_id']);

                $allowed_values[$subcategory_id] = [
                    'uri' => route('subcategory.show', ['resource_type_id' => $this->resource_type_id, 'category_id' => $category_id, 'subcategory_id' => $subcategory_id], false),
                    'value' => $subcategory_id,
                    'name' => $subcategory['subcategory_name'],
                    'description' => trans('item-type-allocated-expense/allowed-values.description-prefix-subcategory') .
                        $subcategory['subcategory_name'] . trans('item-type-allocated-expense/allowed-values.description-suffix-subcategory')
                ];
            }
        }

        return $allowed_values;
    }
}
