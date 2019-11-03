<?php
declare(strict_types=1);

namespace App\Models\Summary\ItemType;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

/**
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class AllocatedExpense extends Model
{
    protected $guarded = ['id', 'actualised_total', 'created_at', 'updated_at'];
    protected $table = 'item';
    protected $sub_table = 'item_type_allocated_expense';

    /**
     * Return the summary of items, grouped by category
     *
     * @param integer $resource_type_id
     * @param integer $resource_id
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
                SUM({$this->sub_table}.actualised_total) AS total")->
            join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            join("item_category", "item_category.item_id", "item.id")->
            join("category", "category.id", "item_category.category_id")->
            where("category.resource_type_id", "=", $resource_type_id)->
            where("resource_type.id", "=", $resource_type_id)->
            where("resource.id", "=", $resource_id);

        $collection = $this->includeUnpublished($collection, $parameters);

        return $collection->groupBy("item_category.category_id")->
            orderBy("name")->
            get()->
            toArray();
    }

    /**
     * Return the summary for a specific category
     *
     * @param integer $resource_type_id
     * @param integer $resource_id
     * @param integer $category_id
     * @param array $parameters
     *
     * @return array
     */
    public function categorySummary(
        int $resource_type_id,
        int $resource_id, $category_id,
        array $parameters = []
    ): array
    {
        $collection = $this->
            selectRaw("
                category.id, 
                category.name AS name, 
                category.description AS description, 
                SUM({$this->sub_table}.actualised_total) AS total")->
            join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            join("item_category", "item_category.item_id", "item.id")->
            join("category", "category.id", "item_category.category_id")->
            where("category.resource_type_id", "=", $resource_type_id)->
            where("resource_type.id", "=", $resource_type_id)->
            where("resource.id", "=", $resource_id)->
            where("category.id", "=", $category_id);

        $collection = $this->includeUnpublished($collection, $parameters);

        return $collection->groupBy("item_category.category_id")->
            orderBy("name")->
            get()->
            toArray();
    }

    /**
     * Return a filter summary
     *
     * @param integer $resource_type_id
     * @param integer $resource_id
     * @param integer|null $category_id
     * @param integer|null $subcategory_id
     * @param integer|null $year
     * @param integer|null $month
     * @param array $parameters
     * @param array $search_parameters
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
        array $search_parameters = []
    ): array
    {
        $collection = $this->
            selectRaw("SUM({$this->sub_table}.actualised_total) AS total")->
            join($this->sub_table, 'item.id', 'item_type_allocated_expense.item_id')->
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
        if ($year !== null) {
            $collection->whereRaw(DB::raw("YEAR({$this->sub_table}.effective_date) = {$year}"));
        }
        if ($month !== null) {
            $collection->whereRaw(DB::raw("MONTH({$this->sub_table}.effective_date) = {$month}"));
        }
        if (count($search_parameters) > 0) {
            foreach ($search_parameters as $field => $search_term) {
                $collection->where("{$this->sub_table}." . $field, 'LIKE', '%' . $search_term . '%');
            }
        }

        $collection = $this->includeUnpublished($collection, $parameters);

        return $collection->get()->
            toArray();
    }

    /**
     * Return a monthly summary
     *
     * @param integer $resource_type_id
     * @param integer $resource_id
     * @param integer $year
     * @param array $parameters
     *
     * @return array
     */
    public function monthsSummary(
        int $resource_type_id,
        int $resource_id,
        int $year,
        array $parameters = []
    ): array
    {
        $collection = $this->
            selectRaw("MONTH({$this->sub_table}.effective_date) as month, SUM({$this->sub_table}.actualised_total) AS total")->
            join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where("resource.id", "=", $resource_id)->
            whereRaw(DB::raw("YEAR({$this->sub_table}.effective_date) = '{$year}'"));

        $collection = $this->includeUnpublished($collection, $parameters);

        return $collection->groupBy("month")->
            orderBy("month")->
            get()->
            toArray();
    }

    /**
     * Return a summary for a specific month
     *
     * @param integer $resource_type_id
     * @param integer $resource_id
     * @param integer $year
     * @param integer $month
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
    ): array
    {
        $collection = $this->
            selectRaw("MONTH({$this->sub_table}.effective_date) as month, SUM({$this->sub_table}.actualised_total) AS total")->
            join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where("resource.id", "=", $resource_id)->
            whereRaw(DB::raw("YEAR({$this->sub_table}.effective_date) = '{$year}'"))->
            whereRaw(DB::raw("MONTH({$this->sub_table}.effective_date) = '{$month}'"));

        $collection = $this->includeUnpublished($collection, $parameters);

        return $collection->groupBy("month")->
            orderBy("month")->
            get()->
            toArray();
    }

    public function subCategoriesSummary(
        int $resource_type_id,
        int $resource_id,
        int $category_id,
        bool $include_unpublished = false
    ): array
    {
        $collection = $this->
            selectRaw('
                sub_category.id, 
                sub_category.name AS name, 
                sub_category.description AS description,
                SUM(item_type_allocated_expense.actualised_total) AS total')->
            join('item_type_allocated_expense', 'item.id', 'item_type_allocated_expense.item_id')->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            join("item_category", "item_category.item_id", "item.id")->
            join("item_sub_category", "item_sub_category.item_category_id", "item_category.id")->
            join("category", "category.id", "item_category.category_id")->
            join("sub_category", "sub_category.id", "item_sub_category.sub_category_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where("resource.id", "=", $resource_id)->
            where("category.id", "=", $category_id);

        $collection = $this->includeUnpublished($collection, $include_unpublished);

        return $collection->groupBy("item_sub_category.sub_category_id")->
            orderBy("name")->
            get()->
            toArray();
    }

    public function subCategorySummary(
        int $resource_type_id,
        int $resource_id,
        int $category_id,
        int $subcategory_id,
        bool $include_unpublished = false
    ): array
    {
        $collection = $this->
            selectRaw('
                sub_category.id, 
                sub_category.name AS name, 
                sub_category.description AS description,
                SUM(item_type_allocated_expense.actualised_total) AS total')->
            join('item_type_allocated_expense', 'item.id', 'item_type_allocated_expense.item_id')->
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

        $collection = $this->includeUnpublished($collection, $include_unpublished);

        return $collection->groupBy("item_sub_category.sub_category_id")->
            orderBy("name")->
            get()->
            toArray();
    }

    /**
     * Return the summary for items
     *
     * @param int $resource_type_id
     * @param int $resource_id
     * @param boolean $include_unpublished
     *
     * @return array
     */
    public function summary(
        int $resource_type_id,
        int $resource_id,
        bool $include_unpublished = false
    ): array
    {
        $collection = $this->selectRaw('sum(item_type_allocated_expense.actualised_total) AS actualised_total')->
            join('item_type_allocated_expense', 'item.id', 'item_type_allocated_expense.item_id')->
            join('resource', 'item.resource_id', 'resource.id')->
            where('resource_id', '=', $resource_id)->
            where('resource.resource_type_id', '=', $resource_type_id);

        $collection = $this->includeUnpublished($collection, $include_unpublished);

        return $collection->get()
            ->toArray();
    }

    public function yearsSummary(
        int $resource_type_id,
        int $resource_id,
        bool $include_unpublished = false
    ): array
    {
        $collection = $this->
            selectRaw("YEAR(item_type_allocated_expense.effective_date) as year, SUM(item_type_allocated_expense.actualised_total) AS total")->
            join('item_type_allocated_expense', 'item.id', 'item_type_allocated_expense.item_id')->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where("resource.id", "=", $resource_id);

        $collection = $this->includeUnpublished($collection, $include_unpublished);

        return $collection->groupBy("year")->
            orderBy("year")->
            get()->
            toArray();
    }

    public function yearSummary(
        int $resource_type_id,
        int $resource_id,
        int $year,
        bool $include_unpublished = false
    ): array
    {
        $collection = $this->
            selectRaw("YEAR(item_type_allocated_expense.effective_date) as year, SUM(item_type_allocated_expense.actualised_total) AS total")->
            join('item_type_allocated_expense', 'item.id', 'item_type_allocated_expense.item_id')->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where("resource.id", "=", $resource_id)->
            whereRaw(DB::raw("YEAR(item_type_allocated_expense.effective_date) = '{$year}'"));

        $collection = $this->includeUnpublished($collection, $include_unpublished);

        return $collection->groupBy("year")->
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
            array_key_exists('include-unpublished', $parameters) === true &&
            $parameters['include-unpublished'] === true
        ) {
            $collection->where(function ($sql) {
                $sql->whereNull('item_type_allocated_expense.publish_after')->
                    orWhereRaw('item_type_allocated_expense.publish_after < NOW()');
            });
        }

        return $collection;
    }
}
