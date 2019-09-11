<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemSummary extends Model
{
    protected $guarded = ['id', 'actualised_total', 'created_at', 'updated_at'];
    protected $table = 'item';

    /**
     * Return the summary of items, grouped by category
     *
     * @param int $resource_type_id
     * @param int $resource_id
     * @param boolean $include_unpublished
     *
     * @return mixed
     */
    public function categoriesSummary(
        int $resource_type_id,
        int $resource_id,
        bool $include_unpublished = false
    ): array
    {
        $collection = $this->
            selectRaw('
                category.id, 
                category.name AS name, 
                category.description AS description,
                SUM(item.actualised_total) AS total')->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            join("item_category", "item_category.item_id", "item.id")->
            join("category", "category.id", "item_category.category_id")->
            where("category.resource_type_id", "=", $resource_type_id)->
            where("resource_type.id", "=", $resource_type_id)->
            where("resource.id", "=", $resource_id);

        $collection = $this->includeUnpublished($collection, $include_unpublished);

        return $collection->groupBy("item_category.category_id")->
            orderBy("name")->
            get()->
            toArray();
    }

    public function categorySummary(
        int $resource_type_id,
        int $resource_id, $category_id,
        bool $include_unpublished
    ): array
    {
        $collection = $this->
            selectRaw('
                category.id, 
                category.name AS name, 
                category.description AS description, 
                SUM(item.actualised_total) AS total')->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            join("item_category", "item_category.item_id", "item.id")->
            join("category", "category.id", "item_category.category_id")->
            where("category.resource_type_id", "=", $resource_type_id)->
            where("resource_type.id", "=", $resource_type_id)->
            where("resource.id", "=", $resource_id)->
            where("category.id", "=", $category_id);

        $collection = $this->includeUnpublished($collection, $include_unpublished);

        return $collection->groupBy("item_category.category_id")->
            orderBy("name")->
            get()->
            toArray();
    }

    public function filteredSummary(
        int $resource_type_id,
        int $resource_id,
        int $category_id = null,
        int $subcategory_id = null,
        int $year = null,
        int $month = null,
        array $search_parameters = [],
        bool $include_unpublished = false
    ): array
    {
        $collection = $this->
            selectRaw('SUM(item.actualised_total) AS total')->
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
            $collection->whereRaw(\DB::raw("YEAR(item.effective_date) = {$year}"));
        }
        if ($month !== null) {
            $collection->whereRaw(\DB::raw("MONTH(item.effective_date) = {$month}"));
        }
        if (count($search_parameters) > 0) {
            foreach ($search_parameters as $field => $search_term) {
                $collection->where('item.' . $field, 'LIKE', '%' . $search_term . '%');
            }
        }

        $collection = $this->includeUnpublished($collection, $include_unpublished);

        return $collection->get()->
            toArray();
    }

    public function monthsSummary(
        int $resource_type_id,
        int $resource_id,
        int $year,
        bool $include_unpublished = false
    ): array
    {
        $collection = $this->
            selectRaw("MONTH(item.effective_date) as month, SUM(item.actualised_total) AS total")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where("resource.id", "=", $resource_id)->
            whereRaw(\DB::raw("YEAR(item.effective_date) = '{$year}'"));

        $collection = $this->includeUnpublished($collection, $include_unpublished);

        return $collection->groupBy("month")->
            orderBy("month")->
            get()->
            toArray();
    }

    public function monthSummary(
        int $resource_type_id,
        int $resource_id,
        int $year,
        int $month,
        bool $include_unpublished = false
    )
    {
        $collection = $this->
            selectRaw("MONTH(item.effective_date) as month, SUM(item.actualised_total) AS total")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where("resource.id", "=", $resource_id)->
            whereRaw(\DB::raw("YEAR(item.effective_date) = '{$year}'"))->
            whereRaw(\DB::raw("MONTH(item.effective_date) = '{$month}'"));

        $collection = $this->includeUnpublished($collection, $include_unpublished);

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
                SUM(item.actualised_total) AS total')->
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
                SUM(item.actualised_total) AS total')->
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
        $collection = $this->selectRaw('sum(item.actualised_total) AS actualised_total')->
            where('resource_id', '=', $resource_id)->
            join('resource', 'item.resource_id', 'resource.id')->
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
            selectRaw("YEAR(item.effective_date) as year, SUM(item.actualised_total) AS total")->
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
            selectRaw("YEAR(item.effective_date) as year, SUM(item.actualised_total) AS total")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where("resource.id", "=", $resource_id)->
            whereRaw(\DB::raw("YEAR(item.effective_date) = '{$year}'"));

        $collection = $this->includeUnpublished($collection, $include_unpublished);

        return $collection->groupBy("year")->
            get()->
            toArray();
    }

    /**
     * Work out if we should be hiding unpublished items, by default we don't show them
     *
     * @param $collection
     * @param boolean $include_unpublished
     *
     * @return Builder
     */
    private function includeUnpublished($collection, bool $include_unpublished): Builder
    {
        if ($include_unpublished === false) {
            $collection->where(function ($sql) {
                $sql->whereNull('item.publish_after')->
                    orWhereRaw('item.publish_after < NOW()');
            });
        }

        return $collection;
    }
}
