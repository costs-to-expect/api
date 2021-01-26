<?php
declare(strict_types=1);

namespace App\ItemType\SimpleExpense;

use Illuminate\Database\Eloquent\Model as LaravelModel;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2021
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SummaryResourceTypeModel extends LaravelModel
{
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $table = 'item';
    protected $sub_table = 'item_type_simple_expense';

    /**
     * Return the summary for all items for the resources in the requested resource type
     *
     * @param int $resource_type_id
     * @param array $parameters
     *
     * @return array
     */
    public function summary(
        int $resource_type_id,
        array $parameters
    ): array
    {
        $collection = $this
            ->selectRaw("
                sum({$this->sub_table}.total) AS total, 
                currency.code AS currency_code,
                COUNT({$this->sub_table}.item_id) AS total_count
            ")
            ->selectRaw("
                (
                    SELECT 
                        GREATEST(
                            MAX(`{$this->sub_table}`.`created_at`), 
                            IFNULL(MAX(`{$this->sub_table}`.`updated_at`), 0)
                        )
                    FROM 
                        `{$this->sub_table}` 
                    JOIN 
                        `item` ON 
                            `{$this->sub_table}`.`item_id` = `{$this->table}`.`id`
                    JOIN 
                        `resource` ON 
                            `{$this->table}`.`resource_id` = `resource`.`id`
                    WHERE
                        `resource`.`resource_type_id` = ? 
                ) AS `last_updated`",
                [
                    $resource_type_id
                ]
            )
            ->join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")
            ->join('resource', 'item.resource_id', 'resource.id')
            ->join('resource_type', 'resource.resource_type_id', 'resource_type.id')
            ->join('currency', "{$this->sub_table}.currency_id", 'currency.id')
            ->where('resource_type.id', '=', $resource_type_id);

        return $collection
            ->groupBy('currency.code')
            ->get()
            ->toArray();
    }

    /**
     * Return the summary for all items for the resources in the requested resource
     * type grouped by resource
     *
     * @param int $resource_type_id
     * @param array $parameters
     *
     * @return array
     */
    public function resourcesSummary(
        int $resource_type_id,
        array $parameters
    ): array
    {
        $collection = $this
            ->selectRaw("
                resource.id AS id, 
                resource.name AS `name`, 
                currency.code AS currency_code,
                SUM({$this->sub_table}.total) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count, 
                MAX({$this->sub_table}.created_at) AS last_updated"
            )
            ->join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")
            ->join('resource', 'item.resource_id', 'resource.id')
            ->join('resource_type', 'resource.resource_type_id', 'resource_type.id')
            ->join('currency', "{$this->sub_table}.currency_id", 'currency.id')
            ->where('resource_type.id', '=', $resource_type_id);

        return $collection
            ->groupBy('resource.id', 'currency.code')
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    /**
     * Return the summary for all items for the resources in the requested resource
     * type grouped by category
     *
     * @param integer $resource_type_id
     * @param array $parameters
     *
     * @return array
     */
    public function categoriesSummary(
        int $resource_type_id,
        array $parameters
    ): array
    {
        $collection = $this
            ->selectRaw("
                category.id, 
                category.name AS name, 
                category.description AS description,
                currency.code AS currency_code,
                SUM({$this->sub_table}.total) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count, 
                MAX({$this->sub_table}.created_at) AS last_updated")
            ->join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")
            ->join("resource", "resource.id", "item.resource_id")
            ->join("resource_type", "resource_type.id", "resource.resource_type_id")
            ->join("item_category", "item_category.item_id", "item.id")
            ->join("category", "category.id", "item_category.category_id")
            ->join('currency', "{$this->sub_table}.currency_id", 'currency.id')
            ->where("category.resource_type_id", "=", $resource_type_id)
            ->where("resource_type.id", "=", $resource_type_id);

        return $collection
            ->groupBy('category.id', 'currency.code')
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    /**
     * Return the summary for all items for the resources in the requested resource
     * type for the requested category
     *
     * @param integer $resource_type_id
     * @param integer $category_id
     * @param array $parameters
     *
     * @return array
     */
    public function categorySummary(
        int $resource_type_id,
        int $category_id,
        array $parameters
    ): array
    {
        $collection = $this
            ->selectRaw("
                category.id, 
                category.name AS name, 
                category.description, 
                currency.code AS currency_code,
                SUM({$this->sub_table}.total) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count, 
                MAX({$this->sub_table}.created_at) AS last_updated")
            ->join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")
            ->join("resource", "resource.id", "item.resource_id")
            ->join("resource_type", "resource_type.id", "resource.resource_type_id")
            ->join("item_category", "item_category.item_id", "item.id")
            ->join("category", "category.id", "item_category.category_id")
            ->join('currency', "{$this->sub_table}.currency_id", 'currency.id')
            ->where("category.resource_type_id", "=", $resource_type_id)
            ->where("resource_type.id", "=", $resource_type_id)
            ->where("category.id", '=', $category_id);

        return $collection
            ->groupBy('category.id', 'currency.code')
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function filteredSummary(
        int $resource_type_id,
        int $category_id = null,
        int $subcategory_id = null,
        array $parameters = [],
        array $search_parameters = []
    ): array
    {
        $collection = $this
            ->selectRaw("
                SUM({$this->sub_table}.total) AS total,
                currency.code AS currency_code,
                COUNT({$this->sub_table}.item_id) AS total_count,  
                MAX({$this->sub_table}.created_at) AS last_updated
            ")
            ->join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")
            ->join("resource", "resource.id", "item.resource_id")
            ->join("resource_type", "resource_type.id", "resource.resource_type_id")
            ->join("item_category", "item_category.item_id", "item.id")
            ->join("item_sub_category", "item_sub_category.item_category_id", "item_category.id")
            ->join("category", "category.id", "item_category.category_id")
            ->join("sub_category", "sub_category.id", "item_sub_category.sub_category_id")
            ->join('currency', "{$this->sub_table}.currency_id", 'currency.id')
            ->where("resource_type.id", "=", $resource_type_id);

        if ($category_id !== null) {
            $collection->where("category.id", "=", $category_id);
        }
        if ($subcategory_id !== null) {
            $collection->where("sub_category.id", "=", $subcategory_id);
        }
        if (count($search_parameters) > 0) {
            foreach ($search_parameters as $field => $search_term) {
                $collection->where("{$this->sub_table}." . $field, 'LIKE', '%' . $search_term . '%');
            }
        }

        return $collection
            ->groupBy('currency.code')
            ->get()
            ->toArray();
    }

    /**
     * Return the summary for all items for the resources in the requested resource
     * type and category grouped by subcategory
     *
     * @param int $resource_type_id
     * @param int $category_id
     * @param array $parameters
     *
     * @return array
     */
    public function subcategoriesSummary(
        int $resource_type_id,
        int $category_id,
        array $parameters
    ): array
    {
        $collection = $this
            ->selectRaw("
                sub_category.id, 
                sub_category.name AS name, 
                sub_category.description AS description,
                currency.code AS currency_code, 
                SUM({$this->sub_table}.total) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count, 
                MAX({$this->sub_table}.created_at) AS last_updated
            ")
            ->join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")
            ->join("resource", "resource.id", "item.resource_id")
            ->join("resource_type", "resource_type.id", "resource.resource_type_id")
            ->join("item_category", "item_category.item_id", "item.id")
            ->join("item_sub_category", "item_sub_category.item_category_id", "item_category.id")
            ->join("category", "category.id", "item_category.category_id")
            ->join("sub_category", "sub_category.id", "item_sub_category.sub_category_id")
            ->join('currency', "{$this->sub_table}.currency_id", 'currency.id')
            ->where("category.resource_type_id", "=", $resource_type_id)
            ->where("resource_type.id", "=", $resource_type_id)
            ->where("category.id", "=", $category_id);

        return $collection
            ->groupBy('sub_category.id', 'currency.code')
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    /**
     * Return the summary for all items for the resources in the requested resource
     * type and category and subcategory
     *
     * @param int $resource_type_id
     * @param int $category_id
     * @param int $subcategory_id
     * @param array $parameters
     *
     * @return array
     */
    public function subcategorySummary(
        int $resource_type_id,
        int $category_id,
        int $subcategory_id,
        array $parameters
    ): array
    {
        $collection = $this
            ->selectRaw("
                sub_category.id, 
                sub_category.name AS name, 
                sub_category.description AS description, 
                currency.code AS currency_code,
                SUM($this->sub_table.total) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count, 
                MAX({$this->sub_table}.created_at) AS last_updated
            ")
            ->join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")
            ->join("resource", "resource.id", "item.resource_id")
            ->join("resource_type", "resource_type.id", "resource.resource_type_id")
            ->join("item_category", "item_category.item_id", "item.id")
            ->join("item_sub_category", "item_sub_category.item_category_id", "item_category.id")
            ->join("category", "category.id", "item_category.category_id")
            ->join("sub_category", "sub_category.id", "item_sub_category.sub_category_id")
            ->join('currency', "{$this->sub_table}.currency_id", 'currency.id')
            ->where("category.resource_type_id", "=", $resource_type_id)
            ->where("resource_type.id", "=", $resource_type_id)
            ->where("category.id", "=", $category_id)
            ->where('sub_category.id', '=', $subcategory_id);

        return $collection
            ->groupBy('sub_category.id', 'currency.code')
            ->orderBy('name')
            ->get()
            ->toArray();
    }
}
