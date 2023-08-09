<?php

declare(strict_types=1);

namespace App\ItemType\AllocatedExpense\Models;

use App\Models\Utility;
use App\Models\Currency;
use App\HttpRequest\Validate\Boolean;
use Illuminate\Database\Eloquent\Model as LaravelModel;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @mixin QueryBuilder
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Item extends LaravelModel
{
    protected $table = 'item_type_allocated_expense';

    protected $guarded = ['id', 'actualised_total'];

    public $timestamps = false;

    public function currency()
    {
        return $this->hasOne(Currency::class, 'id', 'currency_id');
    }

    public function setActualisedTotal($total, $percentage)
    {
        $this->attributes['actualised_total'] = ($percentage === 100) ? $total : $total * ($percentage/100);
    }

    public function instance(int $item_id): ?Item
    {
        return $this->where('item_id', '=', $item_id)->
            select(
                'item_type_allocated_expense.id',
                'item_type_allocated_expense.percentage',
                'item_type_allocated_expense.total'
            )->
            first();
    }

    /**
     * @param integer $resource_type_id
     * @param integer $resource_id
     * @param integer $item_id
     * @param array $parameters
     *
     * @return array|null
     */
    public function single(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        array $parameters = []
    ): ?array {
        $fields = [
            "item.id AS item_id",
            "{$this->table}.name AS item_name",
            "{$this->table}.description AS item_description",
            "currency.id AS item_currency_id",
            "currency.code AS item_currency_code",
            "currency.name AS item_currency_name",
            "{$this->table}.effective_date AS item_effective_date",
            "{$this->table}.total AS item_total",
            "{$this->table}.percentage AS item_percentage",
            "{$this->table}.actualised_total AS item_actualised_total",
            "{$this->table}.created_at AS item_created_at",
            "{$this->table}.updated_at AS item_updated_at"
        ];

        $result = $this->from('item')->
            join('item_type_allocated_expense', 'item.id', 'item_type_allocated_expense.item_id')->
            join('resource', 'item.resource_id', 'resource.id')->
            join('currency', 'item_type_allocated_expense.currency_id', 'currency.id')->
            where('resource_id', '=', $resource_id)->
            where('resource.resource_type_id', '=', $resource_type_id)->
            where('item_type_allocated_expense.item_id', '=', $item_id)->
            where('item.id', '=', $item_id);

        // We can only do this here because there will only ever be one category assignment
        if (
            array_key_exists('include-categories', $parameters) === true &&
            Boolean::convertedValue($parameters['include-categories']) === true
        ) {
            $result->leftJoin('item_category', 'item.id', 'item_category.item_id')->
                leftJoin('category', 'item_category.category_id', 'category.id');

            $fields[] = 'item_category.id AS item_category_id';
            $fields[] = 'category.id AS category_id';
            $fields[] = 'category.name AS category_name';
            $fields[] = 'category.description AS category_description';

            if (
                array_key_exists('include-subcategories', $parameters) === true &&
                Boolean::convertedValue($parameters['include-subcategories']) === true
            ) {
                $result->leftJoin('item_sub_category', 'item_category.id', 'item_sub_category.item_category_id')->
                leftJoin('sub_category', 'item_sub_category.sub_category_id', 'sub_category.id');

                $fields[] = 'item_sub_category.id AS item_subcategory_id';
                $fields[] = 'sub_category.id AS subcategory_id';
                $fields[] = 'sub_category.name AS subcategory_name';
                $fields[] = 'sub_category.description AS subcategory_description';
            }
        }

        $item = $result->select($fields)->first();

        if ($item !== null) {
            return $item->toArray();
        }

        return null;
    }

    /**
     * Work out the maximum effective date year for the requested resource id,
     * defaults to the current year if no data exists
     *
     * @param integer $resource_id
     *
     * @return integer
     */
    public function maximumEffectiveDateYear(int $resource_id): int
    {
        $result = $this->join('item', 'item_type_allocated_expense.item_id', 'item.id')->
            where('item.resource_id', '=', $resource_id)->
            selectRaw('YEAR(MAX(`item_type_allocated_expense`.`effective_date`)) AS `year_limit`')->
            first();

        if ($result === null) {
            return (int) (date('Y'));
        }

        return (int) ($result->year_limit);
    }

    /**
     * Work out the minimum effective date year for the requested resource id,
     * defaults to the current year if no data exists
     *
     * @param integer $resource_id
     *
     * @return integer
     */
    public function minimumEffectiveDateYear(int $resource_id): int
    {
        $result = $this->join('item', 'item_type_allocated_expense.item_id', 'item.id')->
            where('item.resource_id', '=', $resource_id)->
            selectRaw('YEAR(MIN(`item_type_allocated_expense`.`effective_date`)) AS `year_limit`')->
            first();

        if ($result === null) {
            return (int) (date('Y'));
        }

        return (int) ($result->year_limit);
    }

    #[ArrayShape([
        'item_id' => "int",
        'item_name' => "string",
        'item_description' => "string",
        'item_effective_date' => "string",
        'item_publish_after' => "string",
        'item_currency_id' => "int",
        'item_currency_code' => "string",
        'item_currency_name' => "string",
        'item_total' => "float",
        'item_percentage' => "float",
        'item_actualised_total' => "float",
        'item_created_at' => "string",
        'item_updated_at' => "string"
    ])]
    public function instanceToArray(Item $item): array
    {
        return [
            'item_id' => $item->item_id,
            'item_name' => $item->name,
            'item_description' => $item->description,
            'item_effective_date' => $item->effective_date,
            'item_publish_after' => $item->publish_after,
            'item_currency_id' => $item->currency->id,
            'item_currency_code' => $item->currency->code,
            'item_currency_name' => $item->currency->name,
            'item_total' => $item->total,
            'item_percentage' => $item->percentage,
            'item_actualised_total' => $item->actualised_total,
            'item_created_at' => ($item->created_at !== null) ? $item->created_at->toDateTimeString() : null,
            'item_updated_at' => ($item->updated_at !== null) ? $item->updated_at->toDateTimeString() : null,
        ];
    }

    /**
     * Return the total count for the given request, similar to the collection
     * method just without the sorting and only returning a count
     *
     * @param integer $resource_type_id
     * @param integer $resource_id
     * @param array $parameters
     * @param array $search_parameters
     * @param array $filter_parameters
     *
     * @return integer
     */
    public function totalCount(
        int $resource_type_id,
        int $resource_id,
        array $parameters = [],
        array $search_parameters = [],
        array $filter_parameters = []
    ): int {
        $collection = $this->from('item')->
            join('item_type_allocated_expense', 'item.id', 'item_type_allocated_expense.item_id')->
            join('resource', 'item.resource_id', 'resource.id')->
            where('resource_id', '=', $resource_id)->
            where('resource.resource_type_id', '=', $resource_type_id);

        if (
            array_key_exists('year', $parameters) === true &&
            $parameters['year'] !== null
        ) {
            $collection->whereRaw(DB::raw("YEAR(item_type_allocated_expense.effective_date) = '{$parameters['year']}'"));
        }

        if (
            array_key_exists('month', $parameters) === true &&
            $parameters['month'] !== null
        ) {
            $collection->whereRaw(DB::raw("MONTH(item_type_allocated_expense.effective_date) = '{$parameters['month']}'"));
        }

        if (
            array_key_exists('category', $parameters) === true &&
            $parameters['category'] !== null
        ) {
            $collection->join("item_category", "item_category.item_id", "item.id");
            $collection->where('item_category.category_id', '=', $parameters['category']);
        }

        if (
            array_key_exists('category', $parameters) === true &&
            $parameters['category'] !== null &&
            array_key_exists('subcategory', $parameters) === true &&
            $parameters['subcategory'] !== null
        ) {
            $collection->join("item_sub_category", "item_sub_category.item_category_id", "item_category.id");
            $collection->where('item_sub_category.sub_category_id', '=', $parameters['subcategory']);
        }

        $collection = Utility::applySearchClauses(
            $collection,
            $this->table,
            $search_parameters
        );
        $collection = Utility::applyFilteringClauses(
            $collection,
            $this->table,
            $filter_parameters
        );

        $collection = Utility::applyExcludeFutureUnpublishedClause($collection, $parameters);

        return $collection->count();
    }

    /**
     * Return the results for the given request based on the supplied parameters
     *
     * @param integer $resource_type_id
     * @param integer $resource_id
     * @param integer $offset
     * @param integer $limit
     * @param array $parameters
     * @param array $search_parameters
     * @param array $filter_parameters
     * @param array $sort_parameters
     *
     * @return array
     */
    public function paginatedCollection(
        int $resource_type_id,
        int $resource_id,
        int $offset = 0,
        int $limit = 10,
        array $parameters = [],
        array $search_parameters = [],
        array $filter_parameters = [],
        array $sort_parameters = []
    ): array {
        $select_fields = [
            'item.id AS item_id',
            "{$this->table}.name AS item_name",
            "{$this->table}.description AS item_description",
            "{$this->table}.effective_date AS item_effective_date",
            "currency.id AS item_currency_id",
            "currency.code AS item_currency_code",
            "currency.name AS item_currency_name",
            "{$this->table}.total AS item_total",
            "{$this->table}.percentage AS item_percentage",
            "{$this->table}.actualised_total AS item_actualised_total",
            "{$this->table}.created_at AS item_created_at",
            "{$this->table}.updated_at AS item_updated_at"
        ];

        $category_join = false;
        $subcategory_join = false;

        $collection = $this->from('item')->
            join('item_type_allocated_expense', 'item.id', 'item_type_allocated_expense.item_id')->
            join('currency', 'item_type_allocated_expense.currency_id', 'currency.id')->
            join('resource', 'item.resource_id', 'resource.id')->
            where('resource_id', '=', $resource_id)->
            where('resource.resource_type_id', '=', $resource_type_id);

        // We can only do this here because there will only ever be one category assignment
        if (
            array_key_exists('include-categories', $parameters) === true &&
            Boolean::convertedValue($parameters['include-categories']) === true
        ) {
            $collection->leftJoin('item_category', 'item.id', 'item_category.item_id')->
            leftJoin('category', 'item_category.category_id', 'category.id');

            $category_join = true;

            $select_fields[] = 'item_category.id AS item_category_id';
            $select_fields[] = 'category.id AS category_id';
            $select_fields[] = 'category.name AS category_name';
            $select_fields[] = 'category.description AS category_description';

            if (array_key_exists('category', $parameters) === true &&
                $parameters['category'] !== null) {
                $collection->where('item_category.category_id', '=', $parameters['category']);
            }

            if (
                array_key_exists('include-subcategories', $parameters) === true &&
                Boolean::convertedValue($parameters['include-subcategories']) === true
            ) {
                $collection->leftJoin('item_sub_category', 'item_category.id', 'item_sub_category.item_category_id')->
                leftJoin('sub_category', 'item_sub_category.sub_category_id', 'sub_category.id');

                $subcategory_join = true;

                $select_fields[] = 'item_sub_category.id AS item_subcategory_id';
                $select_fields[] = 'sub_category.id AS subcategory_id';
                $select_fields[] = 'sub_category.name AS subcategory_name';
                $select_fields[] = 'sub_category.description AS subcategory_description';

                if (array_key_exists('subcategory', $parameters) === true &&
                    $parameters['subcategory'] !== null) {
                    $collection->where('item_sub_category.sub_category_id', '=', $parameters['subcategory']);
                }
            }
        }

        if (array_key_exists('year', $parameters) === true &&
            $parameters['year'] !== null) {
            $collection->whereRaw(DB::raw("YEAR(item_type_allocated_expense.effective_date) = '{$parameters['year']}'"));
        }

        if (array_key_exists('month', $parameters) === true &&
            $parameters['month'] !== null) {
            $collection->whereRaw(DB::raw("MONTH(item_type_allocated_expense.effective_date) = '{$parameters['month']}'"));
        }

        if (
            array_key_exists('category', $parameters) === true &&
            $parameters['category'] !== null &&
            $category_join === false
        ) {
            $collection->join("item_category", "item_category.item_id", "item.id");
            $collection->where('item_category.category_id', '=', $parameters['category']);
        }

        if (
            array_key_exists('category', $parameters) === true &&
            $parameters['category'] !== null &&
            array_key_exists('subcategory', $parameters) === true &&
            $parameters['subcategory'] !== null &&
            $subcategory_join === false
        ) {
            $collection->join("item_sub_category", "item_sub_category.item_category_id", "item_category.id");
            $collection->where('item_sub_category.sub_category_id', '=', $parameters['subcategory']);
        }

        $collection = Utility::applySearchClauses($collection, 'item_type_allocated_expense', $search_parameters);
        $collection = Utility::applyFilteringClauses($collection, 'item_type_allocated_expense', $filter_parameters);

        $collection = Utility::applyExcludeFutureUnpublishedClause($collection, $parameters);

        if (count($sort_parameters) > 0) {
            foreach ($sort_parameters as $field => $direction) {
                switch ($field) {
                    case 'created':
                        $collection->orderBy('item.created_at', $direction);
                        break;

                    case 'actualised_total':
                    case 'description':
                    case 'effective_date':
                    case 'name':
                    case 'total':
                        $collection->orderBy('item_type_allocated_expense.' . $field, $direction);
                        break;

                    default:
                        $collection->orderBy('item.' . $field, $direction);
                        break;
                }
            }
        } else {
            $collection->orderBy('item_type_allocated_expense.effective_date', 'desc');
            $collection->orderBy('item.created_at', 'desc');
        }

        $collection->offset($offset);
        $collection->limit($limit);

        return $collection
            ->select($select_fields)
            ->selectRaw(
                "
                (
                    SELECT 
                        GREATEST(
                            MAX(`{$this->table}`.`created_at`), 
                            IFNULL(MAX(`{$this->table}`.`updated_at`), 0),
                            0
                        )
                    FROM 
                        `{$this->table}` 
                    JOIN 
                        `item` ON 
                            `{$this->table}`.`item_id` = `item`.`id`
                    WHERE
                        `item`.`resource_id` = ? 
                ) AS `last_updated`",
                [
                    $resource_id
                ]
            )
            ->get()
            ->toArray();
    }

    public function hasCategoryAssignments(int $item_id): bool
    {
        $assignments = $this->from('item')->
            leftJoin('item_category', 'item.id', '=', 'item_category.item_id')->
            leftJoin('item_sub_category', 'item_category.id', '=', 'item_sub_category.item_category_id')->
            where('item.id', '=', $item_id)->
            select('item_category.id AS item_category_id', 'item_sub_category.id AS item_sub_category_id')->
            first();

        if ($assignments !== null) {
            $categories = $assignments->toArray();

            return $categories['item_category_id'] !== null || $categories['item_sub_category_id'] !== null;
        }

        return false;
    }
}
