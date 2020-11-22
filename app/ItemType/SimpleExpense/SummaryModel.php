<?php
declare(strict_types=1);

namespace App\ItemType\SimpleExpense;

use App\Models\Clause;
use Illuminate\Database\Eloquent\Model as LaravelModel;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SummaryModel extends LaravelModel
{
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $table = 'item';
    protected $sub_table = 'item_type_simple_expense';

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
            ->where("resource_type.id", "=", $resource_type_id)
            ->where("resource.id", "=", $resource_id);

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
                MAX({$this->sub_table}.created_at) AS last_updated
            ")
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
        array $parameters = [],
        array $search_parameters = [],
        array $filter_parameters = []
    ): array
    {
        $collection = $this
            ->selectRaw("
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
            ->where("resource_type.id", "=", $resource_type_id)
            ->where("resource.id", "=", $resource_id)
            ->groupBy('currency.code');

        if ($category_id !== null) {
            $collection->where("category.id", "=", $category_id);
        }
        if ($subcategory_id !== null) {
            $collection->where("sub_category.id", "=", $subcategory_id);
        }

        $collection = Clause::applySearch(
            $collection,
            $this->sub_table,
            $search_parameters
        );
        $collection = Clause::applyFiltering(
            $collection,
            $this->sub_table,
            $filter_parameters
        );

        return $collection
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
                MAX({$this->sub_table}.created_at) AS last_updated")
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
            ->where("resource_type.id", "=", $resource_type_id)
            ->where("resource.id", "=", $resource_id)
            ->where("category.id", "=", $category_id)
            ->where("sub_category.id", "=", $subcategory_id);

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
    ): array
    {
        $collection = $this->selectRaw("
                currency.code AS currency_code,
                SUM({$this->sub_table}.total) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count,
                MAX({$this->sub_table}.created_at) AS last_updated
            ")
            ->join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")
            ->join('resource', 'item.resource_id', 'resource.id')
            ->join('currency', "{$this->sub_table}.currency_id", 'currency.id')
            ->where('resource_id', '=', $resource_id)
            ->where('resource.resource_type_id', '=', $resource_type_id)
            ->groupBy('currency.code');

        return $collection
            ->get()
            ->toArray();
    }
}
