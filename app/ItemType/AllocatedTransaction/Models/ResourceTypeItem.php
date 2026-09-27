<?php

declare(strict_types=1);

namespace App\ItemType\AllocatedTransaction\Models;

use App\Models\Utility;
use App\HttpRequest\Validate\Boolean;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Eloquent\Model as LaravelModel;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

/**
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2025
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeItem extends LaravelModel
{
    protected $table = 'item';

    protected $item_table = 'item_type_allocated_transaction';

    protected $guarded = ['id', 'actualised_total', 'created_at', 'updated_at'];

    /**
     * Return the total number of items for the requested resource type
     *
     * @param integer $resource_type_id
     * @param array $parameters_collection
     * @param array $search_parameters
     * @param array $filter_parameters
     *
     * @return integer
     */
    public function totalCount(
        int $resource_type_id,
        array $parameters_collection = [],
        array $search_parameters = [],
        array $filter_parameters = []
    ): int {
        $collection = $this->join('item_type_allocated_transaction', 'item.id', 'item_type_allocated_transaction.item_id')->
            join('resource', 'item.resource_id', 'resource.id')->
            join('resource_type', 'resource.resource_type_id', 'resource_type.id')->
            where('resource_type.id', '=', $resource_type_id);

        if (array_key_exists('year', $parameters_collection) === true &&
            $parameters_collection['year'] !== null) {
            $expression = DB::raw(Utility::yearExpression('item_type_allocated_transaction.effective_date'));
            $collection->whereRaw($expression->getValue(DB::connection()->getQueryGrammar()) . ' = ?', [$parameters_collection['year']]);
        }

        if (array_key_exists('month', $parameters_collection) === true &&
            $parameters_collection['month'] !== null) {
            $expression = DB::raw(Utility::monthExpression('item_type_allocated_transaction.effective_date'));
            $collection->whereRaw($expression->getValue(DB::connection()->getQueryGrammar()) . ' = ?', [$parameters_collection['month']]);
        }

        if (array_key_exists('category', $parameters_collection) === true &&
            $parameters_collection['category'] !== null) {
            $collection->join("item_category", "item_category.item_id", "item.id");
            $collection->where('item_category.category_id', '=', $parameters_collection['category']);

            if (
                array_key_exists('subcategory', $parameters_collection) === true &&
                $parameters_collection['subcategory'] !== null
            ) {
                $collection->join('item_sub_category', 'item_category.id', 'item_sub_category.item_category_id')->
                    join('sub_category', 'item_sub_category.sub_category_id', 'sub_category.id')->
                    where('item_sub_category.sub_category_id', '=', $parameters_collection['subcategory']);
            }
        }

        if (
            array_key_exists('transaction_type', $parameters_collection) === true &&
            $parameters_collection['transaction_type'] !== null
        ) {
            $collection->where('item_type_allocated_transaction.transaction_type', '=', $parameters_collection['transaction_type']);
        }

        $collection = Utility::applySearchClauses(
            $collection,
            $this->item_table,
            $search_parameters
        );

        $collection = Utility::applyFilteringClauses(
            $collection,
            $this->item_table,
            $filter_parameters
        );

        $collection = $this->applyExcludeFutureUnpublishedClause($collection, $parameters_collection);

        return $collection->count();
    }

    /**
     * Return the pagination collection for all the items assigned to the
     * resources for a resource group
     *
     * @param int $resource_type_id
     * @param int $offset
     * @param int $limit
     * @param array $parameters_collection
     * @param array $search_parameters
     * @param array $filter_parameters
     * @param array $sort_parameters
     *
     * @return array
     */
    public function paginatedCollection(
        int $resource_type_id,
        int $offset = 0,
        int $limit = 10,
        array $parameters_collection = [],
        array $search_parameters = [],
        array $filter_parameters = [],
        array $sort_parameters = []
    ): array {
        $select_fields = [
            'resource.id AS resource_id',
            'resource.name AS resource_name',
            'resource.description AS resource_description',
            'item.id AS item_id',
            'item_type_allocated_transaction.name AS item_name',
            'item_type_allocated_transaction.description AS item_description',
            "currency.id AS item_currency_id",
            "currency.code AS item_currency_code",
            "currency.name AS item_currency_name",
            'item_type_allocated_transaction.effective_date AS item_effective_date',
            'item_type_allocated_transaction.total AS item_total',
            'item_type_allocated_transaction.percentage AS item_percentage',
            'item_type_allocated_transaction.actualised_total AS item_actualised_total',
            'item_type_allocated_transaction.transaction_type AS item_transaction_type',
            'item_type_allocated_transaction.created_at AS item_created_at',
            'item_type_allocated_transaction.updated_at AS item_updated_at'
        ];

        $collection = $this->join('item_type_allocated_transaction', 'item.id', 'item_type_allocated_transaction.item_id')->
            join('resource', 'item.resource_id', 'resource.id')->
            join('resource_type', 'resource.resource_type_id', 'resource_type.id')->
            join('currency', 'item_type_allocated_transaction.currency_id', 'currency.id')->
            where('resource_type.id', '=', $resource_type_id);

        $category_join = false; // Check to see if join has taken place
        $subcategory_join = false; // Check to see if join has taken place

        if (
            array_key_exists('include-categories', $parameters_collection) === true &&
            Boolean::convertedValue($parameters_collection['include-categories']) === true
        ) {
            $collection->leftJoin('item_category', 'item.id', 'item_category.item_id')->
                leftJoin('category', 'item_category.category_id', 'category.id');

            $category_join = true;

            $select_fields[] = 'item_category.id AS item_category_id';
            $select_fields[] = 'category.id AS category_id';
            $select_fields[] = 'category.name AS category_name';
            $select_fields[] = 'category.description AS category_description';

            if (array_key_exists('category', $parameters_collection) === true &&
                $parameters_collection['category'] !== null) {
                $collection->where('item_category.category_id', '=', $parameters_collection['category']);
            }

            if (
                array_key_exists('include-subcategories', $parameters_collection) === true &&
                Boolean::convertedValue($parameters_collection['include-subcategories']) === true
            ) {
                $collection->leftJoin('item_sub_category', 'item_category.id', 'item_sub_category.item_category_id')->
                    leftJoin('sub_category', 'item_sub_category.sub_category_id', 'sub_category.id');

                $subcategory_join = true;

                $select_fields[] = 'item_sub_category.id AS item_subcategory_id';
                $select_fields[] = 'sub_category.id AS subcategory_id';
                $select_fields[] = 'sub_category.name AS subcategory_name';
                $select_fields[] = 'sub_category.description AS subcategory_description';

                if (array_key_exists('subcategory', $parameters_collection) === true &&
                    $parameters_collection['subcategory'] !== null) {
                    $collection->where('item_sub_category.sub_category_id', '=', $parameters_collection['subcategory']);
                }
            }
        }

        if (array_key_exists('year', $parameters_collection) === true &&
            $parameters_collection['year'] !== null) {
            $expression = DB::raw(Utility::yearExpression('item_type_allocated_transaction.effective_date'));
            $collection->whereRaw($expression->getValue(DB::connection()->getQueryGrammar()) . ' = ?', [$parameters_collection['year']]);
        }

        if (array_key_exists('month', $parameters_collection) === true &&
            $parameters_collection['month'] !== null) {
            $expression = DB::raw(Utility::monthExpression('item_type_allocated_transaction.effective_date'));
            $collection->whereRaw($expression->getValue(DB::connection()->getQueryGrammar()) . ' = ?', [$parameters_collection['month']]);
        }

        if (array_key_exists('category', $parameters_collection) === true &&
            $parameters_collection['category'] !== null &&
            $category_join === false) {
            $collection->join('item_category', 'item.id', 'item_category.item_id')->
                join('category', 'item_category.category_id', 'category.id')->
                where('item_category.category_id', '=', $parameters_collection['category']);

            if (array_key_exists('subcategory', $parameters_collection) === true &&
                $parameters_collection['subcategory'] !== null &&
                $subcategory_join === false) {
                $collection->join('item_sub_category', 'item_category.id', 'item_sub_category.item_category_id')->
                    join('sub_category', 'item_sub_category.sub_category_id', 'sub_category.id')->
                    where('item_sub_category.sub_category_id', '=', $parameters_collection['subcategory']);
            }
        }

        if (
            array_key_exists('transaction_type', $parameters_collection) === true &&
            $parameters_collection['transaction_type'] !== null
        ) {
            $collection->where('item_type_allocated_transaction.transaction_type', '=', $parameters_collection['transaction_type']);
        }

        $collection = Utility::applySearchClauses(
            $collection,
            $this->item_table,
            $search_parameters
        );

        $collection = Utility::applyFilteringClauses(
            $collection,
            $this->item_table,
            $filter_parameters
        );

        $collection = $this->applyExcludeFutureUnpublishedClause($collection, $parameters_collection);

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
                    case 'transaction_type':
                        $collection->orderBy('item_type_allocated_transaction.' . $field, $direction);
                        break;

                    default:
                        $collection->orderBy('item.' . $field, $direction);
                        break;
                }
            }
        } else {
            $collection->orderBy('item_type_allocated_transaction.effective_date', 'desc');
            $collection->orderBy('item.created_at', 'desc');
        }

        $collection->orderBy('item.id', 'asc');

        $last_updated_expression = $this->lastUpdatedExpression();

        return $collection
            ->offset($offset)
            ->limit($limit)
            ->select($select_fields)
            ->selectRaw($last_updated_expression->getValue(DB::connection()->getQueryGrammar()), [$resource_type_id])
            ->get()
            ->toArray();
    }

    /**
     * Duplicate of Utility::applyExcludeFutureUnpublishedClause() — that shared helper
     * hardcodes the item_type_allocated_expense table name, so it can't be reused here
     */
    private function applyExcludeFutureUnpublishedClause($collection, array $parameters)
    {
        if (
            array_key_exists('include-unpublished', $parameters) === false ||
            Boolean::convertedValue($parameters['include-unpublished']) === false
        ) {
            $collection->where(static function ($collection) {
                $collection
                    ->whereNull('item_type_allocated_transaction.publish_after')
                    ->orWhereRaw('item_type_allocated_transaction.publish_after < CURRENT_TIMESTAMP');
            });
        }

        return $collection;
    }

    private function lastUpdatedExpression(): Expression
    {
        if (DB::getDriverName() === 'mysql') {
            return DB::raw("
                (
                    SELECT
                        GREATEST(
                            MAX(`{$this->item_table}`.`created_at`),
                            IFNULL(MAX(`{$this->item_table}`.`updated_at`), 0),
                            0
                        )
                    FROM
                        `{$this->item_table}`
                    INNER JOIN
                        `item` ON
                            {$this->item_table}.`item_id` = `{$this->table}`.`id`
                    INNER JOIN
                        `resource` ON
                            `item`.`resource_id` = `resource`.`id`
                    WHERE
                        `resource`.`resource_type_id` = ?
                ) AS `last_updated`");
        }

        return DB::raw("(
                SELECT
                    MAX(
                        COALESCE({$this->item_table}.created_at, 0),
                        COALESCE({$this->item_table}.updated_at, 0),
                        0
                    )
                FROM
                    {$this->item_table}
                INNER JOIN
                    item ON
                        {$this->item_table}.item_id = {$this->table}.id
                INNER JOIN
                    resource ON
                        item.resource_id = resource.id
                WHERE
                    resource.resource_type_id = ?
            ) AS last_updated");
    }

    /**
     * Work out the maximum effective date year for the requested
     * resource type id, defaults to the current year if no data exists
     *
     * @param integer $resource_type_id
     *
     * @return integer
     */
    public function maximumEffectiveDateYear(int $resource_type_id): int
    {
        $result = $this->from('item_type_allocated_transaction')->
            join('item', 'item_type_allocated_transaction.item_id', 'item.id')->
            join('resource', 'item.resource_id', 'resource.id')->
            where('resource.resource_type_id', '=', $resource_type_id)->
            selectRaw(Utility::yearExpression('MAX(`item_type_allocated_transaction`.`effective_date`)') . ' AS `year_limit`')->
            first();

        if ($result === null) {
            return (int) date('Y');
        } else {
            return (int) $result->year_limit;
        }
    }

    /**
     * Work out the minimum effective date year for the requested
     * resource type id, defaults to the current year if no data exists
     *
     * @param integer $resource_type_id
     *
     * @return integer
     */
    public function minimumEffectiveDateYear(int $resource_type_id): int
    {
        $result = $this->from('item_type_allocated_transaction')->
            join('item', 'item_type_allocated_transaction.item_id', 'item.id')->
            join('resource', 'item.resource_id', 'resource.id')->
            where('resource.resource_type_id', '=', $resource_type_id)->
            selectRaw(Utility::yearExpression('MIN(`item_type_allocated_transaction`.`effective_date`)') . ' AS `year_limit`')->
            first();

        if ($result === null) {
            return (int) date('Y');
        } else {
            return (int) $result->year_limit;
        }
    }
}
