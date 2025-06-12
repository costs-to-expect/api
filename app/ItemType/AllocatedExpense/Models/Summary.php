<?php

declare(strict_types=1);

namespace App\ItemType\AllocatedExpense\Models;

use App\Models\Utility;
use Illuminate\Database\Eloquent\Model as LaravelModel;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

/**
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Summary extends LaravelModel
{
    protected $guarded = ['id', 'actualised_total', 'created_at', 'updated_at'];
    protected $table = 'item';
    protected string $sub_table = 'item_type_allocated_expense';

    /**
     * Return the summary of items, grouped by category
     *
     * @param int $resource_type_id
     * @param int $resource_id
     * @param array $parameters
     *
     * @return array
     */
    public function categoriesSummary(
        int $resource_type_id,
        int $resource_id,
        array $parameters = []
    ): array {
        $collection = $this->
            selectRaw("
                category.id, 
                category.name AS name, 
                category.description AS description,
                currency.code AS currency_code,
                SUM({$this->sub_table}.actualised_total) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count
            ")
            ->selectRaw(
                "
                (
                    SELECT 
                        GREATEST(
                            MAX(`{$this->sub_table}`.`created_at`), 
                            IFNULL(MAX(`{$this->sub_table}`.`updated_at`), 0),
                            0
                        )
                    FROM 
                        `{$this->sub_table}` 
                    JOIN 
                        `item` ON 
                            `{$this->sub_table}`.`item_id` = `{$this->table}`.`id`
                    WHERE
                        `item`.`resource_id` = ? 
                ) AS `last_updated`",
                [
                    $resource_id
                ]
            )
            ->join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")
            ->join("resource", "resource.id", "item.resource_id")
            ->join("resource_type", "resource_type.id", "resource.resource_type_id")
            ->join("item_category", "item_category.item_id", "item.id")
            ->join("category", "category.id", "item_category.category_id")
            ->join('currency', "{$this->sub_table}.currency_id", 'currency.id')
            ->where("category.resource_type_id", "=", $resource_type_id)
            ->where("resource_type.id", "=", $resource_type_id)
            ->where("resource.id", "=", $resource_id);

        $collection = Utility::applyExcludeFutureUnpublishedClause($collection, $parameters);

        return $collection
            ->groupBy('item_category.category_id', 'currency.code')
            ->orderBy("name")
            ->get()
            ->toArray();
    }

    /**
     * Return the summary for a specific category
     *
     * @param int $resource_type_id
     * @param int $resource_id
     * @param int $category_id
     * @param array $parameters
     *
     * @return array
     */
    public function categorySummary(
        int $resource_type_id,
        int $resource_id,
        int $category_id,
        array $parameters = []
    ): array {
        $collection = $this
            ->selectRaw("
                category.id, 
                category.name AS name, 
                category.description AS description, 
                currency.code AS currency_code,
                SUM({$this->sub_table}.actualised_total) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count
            ")
            ->selectRaw(
                "
                (
                    SELECT 
                        GREATEST(
                            MAX(`{$this->sub_table}`.`created_at`), 
                            IFNULL(MAX(`{$this->sub_table}`.`updated_at`), 0),
                            0
                        )
                    FROM 
                        `{$this->sub_table}` 
                    JOIN 
                        `item` ON 
                            `{$this->sub_table}`.`item_id` = `{$this->table}`.`id`
                    WHERE
                        `item`.`resource_id` = ? 
                ) AS `last_updated`",
                [
                    $resource_id
                ]
            )
            ->join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")
            ->join("resource", "resource.id", "item.resource_id")
            ->join("resource_type", "resource_type.id", "resource.resource_type_id")
            ->join("item_category", "item_category.item_id", "item.id")
            ->join("category", "category.id", "item_category.category_id")
            ->join('currency', "{$this->sub_table}.currency_id", 'currency.id')
            ->where("category.resource_type_id", "=", $resource_type_id)
            ->where("resource_type.id", "=", $resource_type_id)
            ->where("resource.id", "=", $resource_id)
            ->where("category.id", "=", $category_id);

        $collection = Utility::applyExcludeFutureUnpublishedClause($collection, $parameters);

        return $collection
            ->groupBy('item_category.category_id', 'currency.code')
            ->orderBy("name")
            ->get()
            ->toArray();
    }

    public function filteredSummary(
        int $resource_type_id,
        int $resource_id,
        int $category_id = null,
        int $subcategory_id = null,
        int $year = null,
        int $month = null,
        array $parameters = [],
        array $search_parameters = [],
        array $filter_parameters = []
    ): array {
        $collection = $this
            ->selectRaw("
                currency.code AS currency_code,
                SUM({$this->sub_table}.actualised_total) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count
            ")
            ->selectRaw(
                "
                (
                    SELECT 
                        GREATEST(
                            MAX(`{$this->sub_table}`.`created_at`), 
                            IFNULL(MAX(`{$this->sub_table}`.`updated_at`), 0),
                            0
                        )
                    FROM 
                        `{$this->sub_table}` 
                    JOIN 
                        `item` ON 
                            `{$this->sub_table}`.`item_id` = `{$this->table}`.`id`
                    WHERE
                        `item`.`resource_id` = ? 
                ) AS `last_updated`",
                [
                    $resource_id
                ]
            )
            ->join($this->sub_table, 'item.id', 'item_type_allocated_expense.item_id')
            ->join("resource", "resource.id", "item.resource_id")
            ->join("resource_type", "resource_type.id", "resource.resource_type_id")
            ->join("item_category", "item_category.item_id", "item.id")
            ->join("item_sub_category", "item_sub_category.item_category_id", "item_category.id")
            ->join("category", "category.id", "item_category.category_id")
            ->join("sub_category", "sub_category.id", "item_sub_category.sub_category_id")
            ->join('currency', "{$this->sub_table}.currency_id", 'currency.id')
            ->where("resource_type.id", "=", $resource_type_id)
            ->where("resource.id", "=", $resource_id)
            ->groupBy('currency.code');

        if ($category_id !== null) {
            $collection->where("category.id", "=", $category_id);
        }
        if ($subcategory_id !== null) {
            $collection->where("sub_category.id", "=", $subcategory_id);
        }
        if ($year !== null) {
            $expression = DB::raw("YEAR({$this->sub_table}.effective_date) = {$year}");
            $collection->whereRaw($expression->getValue(DB::connection()->getQueryGrammar()));
        }
        if ($month !== null) {
            $expression = DB::raw("MONTH({$this->sub_table}.effective_date) = {$month}");
            $collection->whereRaw($expression->getValue(DB::connection()->getQueryGrammar()));
        }

        $collection = Utility::applySearchClauses(
            $collection,
            $this->sub_table,
            $search_parameters
        );

        $collection = Utility::applyFilteringClauses(
            $collection,
            $this->sub_table,
            $filter_parameters
        );

        $collection = Utility::applyExcludeFutureUnpublishedClause($collection, $parameters);

        return $collection
            ->get()
            ->toArray();
    }

    /**
     * Return a monthly summary
     *
     * @param int $resource_type_id
     * @param int $resource_id
     * @param int $year
     * @param array $parameters
     *
     * @return array
     */
    public function monthsSummary(
        int $resource_type_id,
        int $resource_id,
        int $year,
        array $parameters = []
    ): array {
        $expression = DB::raw("YEAR({$this->sub_table}.effective_date) = '{$year}'");
        
        $collection = $this
            ->selectRaw("
                MONTH({$this->sub_table}.effective_date) as month,
                currency.code AS currency_code, 
                SUM({$this->sub_table}.actualised_total) AS total,            
                COUNT({$this->sub_table}.item_id) AS total_count
            ")
            ->selectRaw(
                "
                (
                    SELECT 
                        GREATEST(
                            MAX(`{$this->sub_table}`.`created_at`), 
                            IFNULL(MAX(`{$this->sub_table}`.`updated_at`), 0),
                            0
                        )
                    FROM 
                        `{$this->sub_table}` 
                    JOIN 
                        `item` ON 
                            `{$this->sub_table}`.`item_id` = `{$this->table}`.`id`
                    WHERE
                        `item`.`resource_id` = ? 
                ) AS `last_updated`",
                [
                    $resource_id
                ]
            )
            ->join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")
            ->join("resource", "resource.id", "item.resource_id")
            ->join("resource_type", "resource_type.id", "resource.resource_type_id")
            ->join('currency', "{$this->sub_table}.currency_id", 'currency.id')
            ->where("resource_type.id", "=", $resource_type_id)
            ->where("resource.id", "=", $resource_id)
            ->whereRaw($expression->getValue(DB::connection()->getQueryGrammar()));

        $collection = Utility::applyExcludeFutureUnpublishedClause($collection, $parameters);

        return $collection
            ->groupBy('month', 'currency.code')
            ->orderBy('month')
            ->get()
            ->toArray();
    }

    /**
     * Return a summary for a specific month
     *
     * @param int $resource_type_id
     * @param int $resource_id
     * @param int $year
     * @param int $month
     * @param array $parameters
     *
     * @return array
     */
    public function monthSummary(
        int $resource_type_id,
        int $resource_id,
        int $year,
        int $month,
        array $parameters = []
    ): array {
        $expression_year = DB::raw("YEAR({$this->sub_table}.effective_date) = '{$year}'");
        $expression_month = DB::raw("MONTH({$this->sub_table}.effective_date) = '{$month}'");
        
        $collection = $this
            ->selectRaw("
                MONTH({$this->sub_table}.effective_date) as month, 
                currency.code AS currency_code,
                SUM({$this->sub_table}.actualised_total) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count
            ")
            ->selectRaw(
                "
                (
                    SELECT 
                        GREATEST(
                            MAX(`{$this->sub_table}`.`created_at`), 
                            IFNULL(MAX(`{$this->sub_table}`.`updated_at`), 0),
                            0
                        )
                    FROM 
                        `{$this->sub_table}` 
                    JOIN 
                        `item` ON 
                            `{$this->sub_table}`.`item_id` = `{$this->table}`.`id`
                    WHERE
                        `item`.`resource_id` = ? 
                ) AS `last_updated`",
                [
                    $resource_id
                ]
            )
            ->join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")
            ->join("resource", "resource.id", "item.resource_id")
            ->join("resource_type", "resource_type.id", "resource.resource_type_id")
            ->join('currency', "{$this->sub_table}.currency_id", 'currency.id')
            ->where("resource_type.id", "=", $resource_type_id)
            ->where("resource.id", "=", $resource_id)
            ->whereRaw($expression_year->getValue(DB::connection()->getQueryGrammar()))
            ->whereRaw($expression_month->getValue(DB::connection()->getQueryGrammar()));

        $collection = Utility::applyExcludeFutureUnpublishedClause($collection, $parameters);

        return $collection
            ->groupBy('month', 'currency.code')
            ->orderBy('month')
            ->get()
            ->toArray();
    }

    /**
     * Subcategories summary
     *
     * @param int $resource_type_id
     * @param int $resource_id
     * @param int $category_id
     * @param array $parameters
     *
     * @return array
     */
    public function subcategoriesSummary(
        int $resource_type_id,
        int $resource_id,
        int $category_id,
        array $parameters = []
    ): array {
        $collection = $this
            ->selectRaw("
                sub_category.id, 
                sub_category.name AS name, 
                sub_category.description AS description,
                currency.code AS currency_code,
                SUM({$this->sub_table}.actualised_total) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count
            ")
            ->selectRaw(
                "
                (
                    SELECT 
                        GREATEST(
                            MAX(`{$this->sub_table}`.`created_at`), 
                            IFNULL(MAX(`{$this->sub_table}`.`updated_at`), 0),
                            0
                        )
                    FROM 
                        `{$this->sub_table}` 
                    JOIN 
                        `item` ON 
                            `{$this->sub_table}`.`item_id` = `{$this->table}`.`id`
                    WHERE
                        `item`.`resource_id` = ? 
                ) AS `last_updated`",
                [
                    $resource_id
                ]
            )
            ->join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")
            ->join("resource", "resource.id", "item.resource_id")
            ->join("resource_type", "resource_type.id", "resource.resource_type_id")
            ->join("item_category", "item_category.item_id", "item.id")
            ->join("item_sub_category", "item_sub_category.item_category_id", "item_category.id")
            ->join("category", "category.id", "item_category.category_id")
            ->join("sub_category", "sub_category.id", "item_sub_category.sub_category_id")
            ->join('currency', "{$this->sub_table}.currency_id", 'currency.id')
            ->where("resource_type.id", "=", $resource_type_id)
            ->where("resource.id", "=", $resource_id)
            ->where("category.id", "=", $category_id);

        $collection = Utility::applyExcludeFutureUnpublishedClause($collection, $parameters);

        return $collection
            ->groupBy('item_sub_category.sub_category_id', 'currency.code')
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    /**
     * Subcategory summary
     *
     * @param int $resource_type_id
     * @param int $resource_id
     * @param int $category_id
     * @param int $subcategory_id
     * @param array $parameters
     *
     * @return array
     */
    public function subcategorySummary(
        int $resource_type_id,
        int $resource_id,
        int $category_id,
        int $subcategory_id,
        array $parameters = []
    ): array {
        $collection = $this
            ->selectRaw("
                sub_category.id, 
                sub_category.name AS name, 
                sub_category.description AS description,
                currency.code AS currency_code,
                SUM({$this->sub_table}.actualised_total) AS total,
                COUNT({$this->sub_table}.item_id) AS total_count
            ")
            ->selectRaw(
                "
                (
                    SELECT 
                        GREATEST(
                            MAX(`{$this->sub_table}`.`created_at`), 
                            IFNULL(MAX(`{$this->sub_table}`.`updated_at`), 0),
                            0
                        )
                    FROM 
                        `{$this->sub_table}` 
                    JOIN 
                        `item` ON 
                            `{$this->sub_table}`.`item_id` = `{$this->table}`.`id`
                    WHERE
                        `item`.`resource_id` = ? 
                ) AS `last_updated`",
                [
                    $resource_id
                ]
            )
            ->join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")
            ->join("resource", "resource.id", "item.resource_id")
            ->join("resource_type", "resource_type.id", "resource.resource_type_id")
            ->join("item_category", "item_category.item_id", "item.id")
            ->join("item_sub_category", "item_sub_category.item_category_id", "item_category.id")
            ->join("category", "category.id", "item_category.category_id")
            ->join("sub_category", "sub_category.id", "item_sub_category.sub_category_id")
            ->join('currency', "{$this->sub_table}.currency_id", 'currency.id')
            ->where("resource_type.id", "=", $resource_type_id)
            ->where("resource.id", "=", $resource_id)
            ->where("category.id", "=", $category_id)
            ->where("sub_category.id", "=", $subcategory_id);

        $collection = Utility::applyExcludeFutureUnpublishedClause($collection, $parameters);

        return $collection
            ->groupBy('item_sub_category.sub_category_id', 'currency.code')
            ->orderBy("name")
            ->get()
            ->toArray();
    }

    /**
     * Return the total summary for all items
     *
     * @param int $resource_type_id
     * @param int $resource_id
     * @param array $parameters
     *
     * @return array
     */
    public function summary(
        int $resource_type_id,
        int $resource_id,
        array $parameters = []
    ): array {
        $collection = $this->selectRaw("
                currency.code AS currency_code,
                SUM({$this->sub_table}.actualised_total) AS total,
                COUNT({$this->sub_table}.item_id) AS total_count
            ")
            ->selectRaw(
                "
                (
                    SELECT 
                        GREATEST(
                            MAX(`{$this->sub_table}`.`created_at`), 
                            IFNULL(MAX(`{$this->sub_table}`.`updated_at`), 0),
                            0
                        )
                    FROM 
                        `{$this->sub_table}` 
                    JOIN 
                        `item` ON 
                            `{$this->sub_table}`.`item_id` = `{$this->table}`.`id`
                    WHERE
                        `item`.`resource_id` = ? 
                ) AS `last_updated`",
                [
                    $resource_id
                ]
            )
            ->join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")
            ->join('resource', 'item.resource_id', 'resource.id')
            ->join('currency', "{$this->sub_table}.currency_id", 'currency.id')
            ->where('resource_id', '=', $resource_id)
            ->where('resource.resource_type_id', '=', $resource_type_id)
            ->groupBy('currency.code');

        $collection = Utility::applyExcludeFutureUnpublishedClause($collection, $parameters);

        return $collection
            ->get()
            ->toArray();
    }

    /**
     * Return the summary grouped by years
     *
     * @param int $resource_type_id
     * @param int $resource_id
     * @param array $parameters
     *
     * @return array
     */
    public function yearsSummary(
        int $resource_type_id,
        int $resource_id,
        array $parameters = []
    ): array {
        $collection = $this
            ->selectRaw("
                YEAR({$this->sub_table}.effective_date) as year, 
                currency.code AS currency_code,
                SUM({$this->sub_table}.actualised_total) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count
            ")
            ->selectRaw(
                "
                (
                    SELECT 
                        GREATEST(
                            MAX(`{$this->sub_table}`.`created_at`), 
                            IFNULL(MAX(`{$this->sub_table}`.`updated_at`), 0),
                            0
                        )
                    FROM 
                        `{$this->sub_table}` 
                    JOIN 
                        `item` ON 
                            `{$this->sub_table}`.`item_id` = `{$this->table}`.`id`
                    WHERE
                        `item`.`resource_id` = ? 
                ) AS `last_updated`",
                [
                    $resource_id
                ]
            )
            ->join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")
            ->join("resource", "resource.id", "item.resource_id")
            ->join("resource_type", "resource_type.id", "resource.resource_type_id")
            ->join('currency', "{$this->sub_table}.currency_id", 'currency.id')
            ->where("resource_type.id", "=", $resource_type_id)
            ->where("resource.id", "=", $resource_id);

        $collection = Utility::applyExcludeFutureUnpublishedClause($collection, $parameters);

        return $collection
            ->groupBy('year', 'currency.code')
            ->orderBy('year')
            ->get()
            ->toArray();
    }

    /**
     * Return a summary for a specific year
     *
     * @param int $resource_type_id
     * @param int $resource_id
     * @param int $year
     * @param array $parameters
     *
     * @return array
     */
    public function yearSummary(
        int $resource_type_id,
        int $resource_id,
        int $year,
        array $parameters = []
    ): array {
        $expression = DB::raw("YEAR({$this->sub_table}.effective_date) = '{$year}'");
        
        $collection = $this
            ->selectRaw("
                YEAR({$this->sub_table}.effective_date) as year, 
                currency.code AS currency_code,
                SUM({$this->sub_table}.actualised_total) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count
            ")
            ->selectRaw(
                "
                (
                    SELECT 
                        GREATEST(
                            MAX(`{$this->sub_table}`.`created_at`), 
                            IFNULL(MAX(`{$this->sub_table}`.`updated_at`), 0),
                            0
                        )
                    FROM 
                        `{$this->sub_table}` 
                    JOIN 
                        `item` ON 
                            `{$this->sub_table}`.`item_id` = `{$this->table}`.`id`
                    WHERE
                        `item`.`resource_id` = ? 
                ) AS `last_updated`",
                [
                    $resource_id
                ]
            )
            ->join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")
            ->join("resource", "resource.id", "item.resource_id")
            ->join("resource_type", "resource_type.id", "resource.resource_type_id")
            ->join('currency', "{$this->sub_table}.currency_id", 'currency.id')
            ->where("resource_type.id", "=", $resource_type_id)
            ->where("resource.id", "=", $resource_id)
            ->whereRaw($expression->getValue(DB::connection()->getQueryGrammar()));

        $collection = Utility::applyExcludeFutureUnpublishedClause($collection, $parameters);

        return $collection
            ->groupBy('year', 'currency.code')
            ->get()
            ->toArray();
    }
}
