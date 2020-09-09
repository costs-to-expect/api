<?php
declare(strict_types=1);

namespace App\Models\ResourceTypeItem\Summary;

use App\Interfaces\ResourceTypeItem\ISummaryModelCategories;
use App\Interfaces\ResourceTypeItem\ISummaryModel;
use App\Models\Clause;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

/**
 * Item model when fetching data by resource type
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class AllocatedExpense extends Model implements ISummaryModel, ISummaryModelCategories
{
    protected $guarded = ['id', 'actualised_total', 'created_at', 'updated_at'];
    protected $table = 'item';
    protected $sub_table = 'item_type_allocated_expense';

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
        $collection = $this->selectRaw("
                SUM({$this->sub_table}.actualised_total) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count, 
                MAX({$this->sub_table}.created_at) AS last_updated
            ")->
            join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")->
            join('resource', 'item.resource_id', 'resource.id')->
            join('resource_type', 'resource.resource_type_id', 'resource_type.id')->
            where('resource_type.id', '=', $resource_type_id);

        $collection = $this->includeUnpublished($collection, $parameters);

        return $collection->
            get()->
            toArray();
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
        $collection = $this->selectRaw("
                resource.id AS id, 
                resource.name AS `name`, 
                SUM({$this->sub_table}.actualised_total) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count, 
                MAX({$this->sub_table}.created_at) AS last_updated"
            )->
            join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")->
            join('resource', 'item.resource_id', 'resource.id')->
            join('resource_type', 'resource.resource_type_id', 'resource_type.id')->
            where('resource_type.id', '=', $resource_type_id);

        $collection = $this->includeUnpublished($collection, $parameters);

        return $collection->groupBy('resource.id')->
            orderBy('name')->
            get()->
            toArray();
    }

    /**
     * Return the summary for all items for the resources in the requested resource
     * type grouped by year
     *
     * @param int $resource_type_id
     * @param array $parameters

     * @return array
     */
    public function yearsSummary(
        int $resource_type_id,
        array $parameters
    ): array
    {
        $collection = $this->selectRaw("
                YEAR({$this->sub_table}.effective_date) as year,
                SUM({$this->sub_table}.actualised_total) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count, 
                MAX({$this->sub_table}.created_at) AS last_updated"
            )->
            join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            where("resource_type.id", "=", $resource_type_id);

        $collection = $this->includeUnpublished($collection, $parameters);

        return $collection->groupBy("year")->
            orderBy("year")->
            get()->
            toArray();
    }

    /**
     * Return the summary for all items for the resources in the requested resource
     * type grouped by month for the requested year
     *
     * @param integer $resource_type_id
     * @param integer $year
     * @param array $parameters
     *
     * @return array
     */
    public function monthsSummary(
        int $resource_type_id,
        int $year,
        array $parameters
    ): array
    {
        $collection = $this->selectRaw("
                MONTH({$this->sub_table}.effective_date) as month, 
                SUM({$this->sub_table}.actualised_total) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count, 
                MAX({$this->sub_table}.created_at) AS last_updated"
            )->
            join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where(DB::raw("YEAR({$this->sub_table}.effective_date)"), '=', $year);

        $collection = $this->includeUnpublished($collection, $parameters);

        return $collection->groupBy("month")->
            orderBy("month")->
            get()->
            toArray();
    }

    /**
     * Return the summary for all items for the resources in the requested resource
     * type for a specific year and month
     *
     * @param integer $resource_type_id
     * @param integer $year
     * @param integer $month
     * @param array $parameters
     *
     * @return array
     */
    public function monthSummary(
        int $resource_type_id,
        int $year,
        int $month,
        array $parameters
    ): array
    {
        $collection = $this->selectRaw("
                MONTH({$this->sub_table}.effective_date) as month, 
                SUM({$this->sub_table}.actualised_total) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count, 
                MAX({$this->sub_table}.created_at) AS last_updated"
            )->
            join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where(DB::raw("YEAR({$this->sub_table}.effective_date)"), '=', $year)->
            where(DB::raw("MONTH({$this->sub_table}.effective_date)"), '=', $month);

        $collection = $this->includeUnpublished($collection, $parameters);

        return $collection->groupBy("month")->
            orderBy("month")->
            get()->
            toArray();
    }

    /**
     * Return the summary for all items for the resources in the requested resource
     * type for a specific year
     *
     * @param integer $resource_type_id
     * @param integer $year
     * @param array $parameters
     *
     * @return array
     */
    public function yearSummary(
        int $resource_type_id,
        int $year,
        array $parameters
    ): array
    {
        $collection = $this->selectRaw("
                YEAR({$this->sub_table}.effective_date) as year, 
                SUM({$this->sub_table}.actualised_total) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count, 
                MAX({$this->sub_table}.created_at) AS last_updated"
            )->
            join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where(DB::raw("YEAR({$this->sub_table}.effective_date)"), '=', $year);

        $collection = $this->includeUnpublished($collection, $parameters);

        return $collection->groupBy("year")->
            orderBy("year")->
            get()->
            toArray();
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
        $collection = $this->selectRaw("
                category.id, 
                category.name AS name, 
                category.description AS description,
                SUM({$this->sub_table}.actualised_total) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count, 
                MAX({$this->sub_table}.created_at) AS last_updated")->
            join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            join("item_category", "item_category.item_id", "item.id")->
            join("category", "category.id", "item_category.category_id")->
            where("category.resource_type_id", "=", $resource_type_id)->
            where("resource_type.id", "=", $resource_type_id);

        $collection = $this->includeUnpublished($collection, $parameters);

        return $collection->groupBy("category.id")->
            orderBy("name")->
            get()->
            toArray();
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
        $collection = $this->selectRaw("
                category.id, 
                category.name AS name, 
                category.description, 
                SUM({$this->sub_table}.actualised_total) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count, 
                MAX({$this->sub_table}.created_at) AS last_updated")->
            join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            join("item_category", "item_category.item_id", "item.id")->
            join("category", "category.id", "item_category.category_id")->
            where("category.resource_type_id", "=", $resource_type_id)->
            where("resource_type.id", "=", $resource_type_id)->
            where("category.id", '=', $category_id);

        $collection = $this->includeUnpublished($collection, $parameters);

        return $collection->groupBy("category.id")->
            orderBy("name")->
            get()->
            toArray();
    }

    /**
     * @param int $resource_type_id
     * @param int|null $category_id
     * @param int|null $subcategory_id
     * @param int|null $year
     * @param int|null $month
     * @param array $parameters
     * @param array $search_parameters
     * @param array $filter_parameters
     * @return array
     */
    public function filteredSummary(
        int $resource_type_id,
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
            selectRaw(
                "SUM({$this->sub_table}.actualised_total) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count, 
                MAX({$this->sub_table}.created_at) AS last_updated")->
            join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            join("item_category", "item_category.item_id", "item.id")->
            join("item_sub_category", "item_sub_category.item_category_id", "item_category.id")->
            join("category", "category.id", "item_category.category_id")->
            join("sub_category", "sub_category.id", "item_sub_category.sub_category_id")->
            where("resource_type.id", "=", $resource_type_id);

        if ($category_id !== null) {
            $collection->where("category.id", "=", $category_id);
        }
        if ($subcategory_id !== null) {
            $collection->where("sub_category.id", "=", $subcategory_id);
        }
        if ($year !== null) {
            $collection->whereRaw(DB::raw("YEAR({$this->sub_table}.effective_date) = {$year}"));
        }
        if ($month !== null) {
            $collection->whereRaw(DB::raw("MONTH({$this->sub_table}.effective_date) = {$month}"));
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

        $collection = $this->includeUnpublished($collection, $parameters);

        return $collection->get()->
            toArray();
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
        $collection = $this->selectRaw("
                sub_category.id, 
                sub_category.name AS name, 
                sub_category.description AS description, 
                SUM({$this->sub_table}.actualised_total) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count, 
                MAX({$this->sub_table}.created_at) AS last_updated")->
            join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            join("item_category", "item_category.item_id", "item.id")->
            join("item_sub_category", "item_sub_category.item_category_id", "item_category.id")->
            join("category", "category.id", "item_category.category_id")->
            join("sub_category", "sub_category.id", "item_sub_category.sub_category_id")->
            where("category.resource_type_id", "=", $resource_type_id)->
            where("resource_type.id", "=", $resource_type_id)->
            where("category.id", "=", $category_id);

        $collection = $this->includeUnpublished($collection, $parameters);

        return $collection->groupBy("sub_category.id")->
            orderBy("name")->
            get()->
            toArray();
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
        $collection = $this->selectRaw("
                sub_category.id, 
                sub_category.name AS name, 
                sub_category.description AS description, 
                SUM($this->sub_table.actualised_total) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count, 
                MAX({$this->sub_table}.created_at) AS last_updated")->
            join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            join("item_category", "item_category.item_id", "item.id")->
            join("item_sub_category", "item_sub_category.item_category_id", "item_category.id")->
            join("category", "category.id", "item_category.category_id")->
            join("sub_category", "sub_category.id", "item_sub_category.sub_category_id")->
            where("category.resource_type_id", "=", $resource_type_id)->
            where("resource_type.id", "=", $resource_type_id)->
            where("category.id", "=", $category_id)->
            where('sub_category.id', '=', $subcategory_id);

        $collection = $this->includeUnpublished($collection, $parameters);

        return $collection->groupBy("sub_category.id")->
            orderBy("name")->
            get()->
            toArray();
    }

    /**
     * Work out if we should be hiding unpublished items, by default we don't show them
     *
     * @param $collection
     * @param array $parameters
     *
     * @return Builder
     */
    private function includeUnpublished($collection, array $parameters): Builder
    {
        if (
            array_key_exists('include-unpublished', $parameters) === false ||
            $parameters['include-unpublished'] === false
        ) {
            $collection->where(static function ($sql) {
                $sql->whereNull('item_type_allocated_expense.publish_after')->
                    orWhereRaw('item_type_allocated_expense.publish_after < NOW()');
            });
        }

        return $collection;
    }
}
