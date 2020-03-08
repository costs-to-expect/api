<?php
declare(strict_types=1);

namespace App\Models\ItemType\Summary;

use App\Interfaces\Item\ISummaryModelCategories;
use App\Interfaces\Item\ISummaryModel\ISummaryModel;
use App\Utilities\Model as ModelUtility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SimpleExpense extends Model implements ISummaryModel, ISummaryModelCategories
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
        $collection = $this->
            selectRaw("
                category.id, 
                category.name AS name, 
                category.description AS description,
                SUM({$this->sub_table}.total) AS total")->
            join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            join("item_category", "item_category.item_id", "item.id")->
            join("category", "category.id", "item_category.category_id")->
            where("category.resource_type_id", "=", $resource_type_id)->
            where("resource_type.id", "=", $resource_type_id)->
            where("resource.id", "=", $resource_id);

        return $collection->groupBy("item_category.category_id")->
            orderBy("name")->
            get()->
            toArray();
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
        $collection = $this->
            selectRaw("
                category.id, 
                category.name AS name, 
                category.description AS description, 
                SUM({$this->sub_table}.total) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count,
                MAX({$this->sub_table}.created_at) AS last_updated
            ")->
            join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            join("item_category", "item_category.item_id", "item.id")->
            join("category", "category.id", "item_category.category_id")->
            where("category.resource_type_id", "=", $resource_type_id)->
            where("resource_type.id", "=", $resource_type_id)->
            where("resource.id", "=", $resource_id)->
            where("category.id", "=", $category_id);

        return $collection->groupBy("item_category.category_id")->
            orderBy("name")->
            get()->
            toArray();
    }

    /**
     * Return a filter summary
     *
     * @param int $resource_type_id
     * @param int $resource_id
     * @param int|null $category_id
     * @param int|null $subcategory_id
     * @param int|null $year
     * @param int|null $month
     * @param array $parameters
     * @param array $search_parameters
     * @param array $filter_parameters
     *
     * @return array
     */
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
    ): array
    {
        $collection = $this->
            selectRaw("
                SUM({$this->sub_table}.total) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count
            ")->
            join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            join("item_category", "item_category.item_id", "item.id")->
            join("item_sub_category", "item_sub_category.item_category_id", "item_category.id")->
            join("category", "category.id", "item_category.category_id")->
            join("sub_category", "sub_category.id", "item_sub_category.sub_category_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where("resource.id", "=", $resource_id);

        if ($category_id !== null) {
            $collection->where("category.id", "=", $category_id);
        }
        if ($subcategory_id !== null) {
            $collection->where("sub_category.id", "=", $subcategory_id);
        }

        $collection = ModelUtility::applySearch(
            $collection,
            $this->sub_table,
            $search_parameters
        );
        $collection = ModelUtility::applyFiltering(
            $collection,
            $this->sub_table,
            $filter_parameters
        );

        return $collection->get()->
            toArray();
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
        $collection = $this->
            selectRaw("
                sub_category.id, 
                sub_category.name AS name, 
                sub_category.description AS description,
                SUM({$this->sub_table}.total) AS total")->
            join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            join("item_category", "item_category.item_id", "item.id")->
            join("item_sub_category", "item_sub_category.item_category_id", "item_category.id")->
            join("category", "category.id", "item_category.category_id")->
            join("sub_category", "sub_category.id", "item_sub_category.sub_category_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where("resource.id", "=", $resource_id)->
            where("category.id", "=", $category_id);

        return $collection->groupBy("item_sub_category.sub_category_id")->
            orderBy("name")->
            get()->
            toArray();
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
        $collection = $this->
            selectRaw("
                sub_category.id, 
                sub_category.name AS name, 
                sub_category.description AS description,
                SUM({$this->sub_table}.total) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count,
                MAX({$this->sub_table}.created_at) AS last_updated
            ")->
            join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            join("item_category", "item_category.item_id", "item.id")->
            join("item_sub_category", "item_sub_category.item_category_id", "item_category.id")->
            join("category", "category.id", "item_category.category_id")->
            join("sub_category", "sub_category.id", "item_sub_category.sub_category_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where("resource.id", "=", $resource_id)->
            where("category.id", "=", $category_id)->
            where("sub_category.id", "=", $subcategory_id);

        return $collection->groupBy("item_sub_category.sub_category_id")->
            orderBy("name")->
            get()->
            toArray();
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
                SUM({$this->sub_table}.total) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count,
                MAX({$this->sub_table}.created_at) AS last_updated
            ")->
            join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")->
            join('resource', 'item.resource_id', 'resource.id')->
            where('resource_id', '=', $resource_id)->
            where('resource.resource_type_id', '=', $resource_type_id);

        return $collection->get()
            ->toArray();
    }
}
