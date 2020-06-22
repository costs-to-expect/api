<?php
declare(strict_types=1);

namespace App\Models;

use App\Utilities\General;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

/**
 * Item model, fetches data by resource type
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeItem extends Model
{
    protected $table = 'item';

    protected $guarded = ['id', 'actualised_total', 'created_at', 'updated_at'];

    /**
     * Return the total number of items for the requested resource type
     *
     * @param integer $resource_type_id
     * @param array $parameters_collection
     * @param array $search_conditions
     *
     * @return integer
     */
    public function totalCount(
        int $resource_type_id,
        array $parameters_collection = [],
        array $search_conditions = []
    ): int
    {
        $collection = $this->join('item_type_allocated_expense', 'item.id', 'item_type_allocated_expense.item_id')->
            join('resource', 'item.resource_id', 'resource.id')->
            join('resource_type', 'resource.resource_type_id', 'resource_type.id')->
            where('resource_type.id', '=', $resource_type_id);

        if (array_key_exists('year', $parameters_collection) === true &&
            $parameters_collection['year'] !== null) {
            $collection->where(DB::raw('YEAR(item_type_allocated_expense.effective_date)'), '=', $parameters_collection['year']);
        }

        if (array_key_exists('month', $parameters_collection) === true &&
            $parameters_collection['month'] !== null) {
            $collection->where(DB::raw('MONTH(item_type_allocated_expense.effective_date)'), '=', $parameters_collection['month']);
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

        if (count($search_conditions) > 0) {
            foreach ($search_conditions as $field => $search_term) {
                $collection->where('item_type_allocated_expense.' . $field, 'LIKE', '%' . $search_term . '%');
            }
        }

        if (
            array_key_exists('include-unpublished', $parameters_collection) === false ||
            $parameters_collection['include-unpublished'] === false
        ) {
            $collection->where(function ($collection) {
                $collection->whereNull('item_type_allocated_expense.publish_after')->
                    orWhereRaw('item_type_allocated_expense.publish_after < NOW()');
            });
        }

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
     * @param array $sort_parameters
     * @param array $search_conditions
     *
     * @return array
     */
    public function paginatedCollection(
        int $resource_type_id,
        int $offset = 0,
        int $limit = 10,
        array $parameters_collection = [],
        array $sort_parameters = [],
        array $search_conditions = []
    ): array
    {
        $select_fields = [
            'resource.id AS resource_id',
            'resource.name AS resource_name',
            'resource.description AS resource_description',
            'item.id AS item_id',
            'item_type_allocated_expense.name AS item_name',
            'item_type_allocated_expense.description AS item_description',
            'item_type_allocated_expense.effective_date AS item_effective_date',
            'item_type_allocated_expense.total AS item_total',
            'item_type_allocated_expense.percentage AS item_percentage',
            'item_type_allocated_expense.actualised_total AS item_actualised_total',
            'item.created_at AS item_created_at'
        ];

        $collection = $this->join('item_type_allocated_expense', 'item.id', 'item_type_allocated_expense.item_id')->
            join('resource', 'item.resource_id', 'resource.id')->
            join('resource_type', 'resource.resource_type_id', 'resource_type.id')->
            where('resource_type.id', '=', $resource_type_id);

        $category_join = false; // Check to see if join has taken place
        $subcategory_join = false; // Check to see if join has taken place

        if (
            array_key_exists('include-categories', $parameters_collection) === true &&
            General::convertedBooleanValue($parameters_collection['include-categories']) === true
        ) {
            $collection->join('item_category', 'item.id', 'item_category.item_id')->
                join('category', 'item_category.category_id', 'category.id');

            $category_join = true;

            $select_fields[] = 'category.id AS category_id';
            $select_fields[] = 'category.name AS category_name';
            $select_fields[] = 'category.description AS category_description';

            if (array_key_exists('category', $parameters_collection) === true &&
                $parameters_collection['category'] !== null) {
                $collection->where('item_category.category_id', '=', $parameters_collection['category']);
            }

            if (
                array_key_exists('include-subcategories', $parameters_collection) === true &&
                General::convertedBooleanValue($parameters_collection['include-subcategories']) === true
            ) {
                $collection->join('item_sub_category', 'item_category.id', 'item_sub_category.item_category_id')->
                    join('sub_category', 'item_sub_category.sub_category_id', 'sub_category.id');

                $subcategory_join = true;

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
            $collection->where(DB::raw('YEAR(item_type_allocated_expense.effective_date)'), '=', $parameters_collection['year']);
        }

        if (array_key_exists('month', $parameters_collection) === true &&
            $parameters_collection['month'] !== null) {
            $collection->where(DB::raw('MONTH(item_type_allocated_expense.effective_date)'), '=', $parameters_collection['month']);
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

        if (count($search_conditions) > 0) {
            foreach ($search_conditions as $field => $search_term) {
                $collection->where('item_type_allocated_expense.' . $field, 'LIKE', '%' . $search_term . '%');
            }
        }

        if (
            array_key_exists('include-unpublished', $parameters_collection) === false ||
            $parameters_collection['include-unpublished'] === false
        ) {
            $collection->where(function ($collection) {
                $collection->whereNull('item_type_allocated_expense.publish_after')->
                    orWhereRaw('item_type_allocated_expense.publish_after < NOW()');
            });
        }

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
        $collection->select($select_fields);

        return $collection->get()->toArray();
    }

    /**
     * Return the summary for all items for the resources in the requested resource type
     *
     * @param int $resource_type_id
     * @param boolean $include_unpublished = false
     *
     * @return array
     */
    public function summary(int $resource_type_id, bool $include_unpublished): array
    {
        $collection = $this->selectRaw('sum(item_type_allocated_expense.actualised_total) AS actualised_total')->
            join('item_type_allocated_expense', 'item.id', 'item_type_allocated_expense.item_id')->
            join('resource', 'item.resource_id', 'resource.id')->
            join('resource_type', 'resource.resource_type_id', 'resource_type.id')->
            where('resource_type.id', '=', $resource_type_id);

        $collection = $this->includeUnpublished($collection, $include_unpublished);

        return $collection->
            get()->
            toArray();
    }

    /**
     * Return the summary for all items for the resources in the requested resource
     * type grouped by resource
     *
     * @param int $resource_type_id
     * @param boolean $include_unpublished
     *
     * @return array
     */
    public function resourcesSummary(int $resource_type_id, bool $include_unpublished): array
    {
        $collection = $this->selectRaw('
                resource.id AS id, 
                resource.name AS `name`, 
                SUM(item_type_allocated_expense.actualised_total) AS total'
            )->
            join('item_type_allocated_expense', 'item.id', 'item_type_allocated_expense.item_id')->
            join('resource', 'item.resource_id', 'resource.id')->
            join('resource_type', 'resource.resource_type_id', 'resource_type.id')->
            where('resource_type.id', '=', $resource_type_id);

        $collection = $this->includeUnpublished($collection, $include_unpublished);

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
     * @param boolean $include_unpublished

     * @return array
     */
    public function yearsSummary(int $resource_type_id, bool $include_unpublished): array
    {
        $collection = $this->selectRaw("
                YEAR(item_type_allocated_expense.effective_date) as year,
                SUM(item_type_allocated_expense.actualised_total) AS total"
            )->
            join('item_type_allocated_expense', 'item.id', 'item_type_allocated_expense.item_id')->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            where("resource_type.id", "=", $resource_type_id);

        $collection = $this->includeUnpublished($collection, $include_unpublished);

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
     * @param boolean $include_unpublished
     *
     * @return array
     */
    public function monthsSummary(int $resource_type_id, int $year, bool $include_unpublished): array
    {
        $collection = $this->selectRaw("
                MONTH(item_type_allocated_expense.effective_date) as month, 
                SUM(item_type_allocated_expense.actualised_total) AS total"
            )->
            join('item_type_allocated_expense', 'item.id', 'item_type_allocated_expense.item_id')->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where(DB::raw('YEAR(item_type_allocated_expense.effective_date)'), '=', $year);

        $collection = $this->includeUnpublished($collection, $include_unpublished);

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
     * @param boolean $include_unpublished
     *
     * @return array
     */
    public function monthSummary(
        int $resource_type_id,
        int $year,
        int $month,
        bool $include_unpublished
    ): array
    {
        $collection = $this->selectRaw("
                MONTH(item_type_allocated_expense.effective_date) as month, 
                SUM(item_type_allocated_expense.actualised_total) AS total"
            )->
            join('item_type_allocated_expense', 'item.id', 'item_type_allocated_expense.item_id')->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where(DB::raw('YEAR(item_type_allocated_expense.effective_date)'), '=', $year)->
            where(DB::raw('MONTH(item_type_allocated_expense.effective_date)'), '=', $month);

        $collection = $this->includeUnpublished($collection, $include_unpublished);

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
     * @param boolean $include_unpublished
     *
     * @return array
     */
    public function yearSummary(int $resource_type_id, int $year, bool $include_unpublished): array
    {
        $collection = $this->selectRaw("
                YEAR(item_type_allocated_expense.effective_date) as year, 
                SUM(item_type_allocated_expense.actualised_total) AS total"
            )->
            join('item_type_allocated_expense', 'item.id', 'item_type_allocated_expense.item_id')->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where(DB::raw('YEAR(item_type_allocated_expense.effective_date)'), '=', $year);

        $collection = $this->includeUnpublished($collection, $include_unpublished);

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
     * @param boolean $include_unpublished
     *
     * @return array
     */
    public function categoriesSummary(
        int $resource_type_id,
        bool $include_unpublished = false
    ): array
    {
        $collection = $this->selectRaw('
                category.id, 
                category.name AS name, 
                category.description AS description,
                SUM(item_type_allocated_expense.actualised_total) AS total')->
            join('item_type_allocated_expense', 'item.id', 'item_type_allocated_expense.item_id')->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            join("item_category", "item_category.item_id", "item.id")->
            join("category", "category.id", "item_category.category_id")->
            where("category.resource_type_id", "=", $resource_type_id)->
            where("resource_type.id", "=", $resource_type_id);

        $collection = $this->includeUnpublished($collection, $include_unpublished);

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
     * @param boolean $include_unpublished
     *
     * @return array
     */
    public function categorySummary(
        int $resource_type_id,
        int $category_id,
        bool $include_unpublished = false
    ): array
    {
        $collection = $this->selectRaw('
                category.id, 
                category.name AS name, 
                category.description, 
                SUM(item_type_allocated_expense.actualised_total) AS total')->
            join('item_type_allocated_expense', 'item.id', 'item_type_allocated_expense.item_id')->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            join("item_category", "item_category.item_id", "item.id")->
            join("category", "category.id", "item_category.category_id")->
            where("category.resource_type_id", "=", $resource_type_id)->
            where("resource_type.id", "=", $resource_type_id)->
            where("category.id", '=', $category_id);

        $collection = $this->includeUnpublished($collection, $include_unpublished);

        return $collection->groupBy("category.id")->
            orderBy("name")->
            get()->
            toArray();
    }

    public function filteredSummary(
        int $resource_type_id,
        int $category_id = null,
        int $subcategory_id = null,
        int $year = null,
        int $month = null,
        array $search_parameters = [],
        bool $include_unpublished = false
    ): array
    {
        $collection = $this->
            selectRaw('SUM(item_type_allocated_expense.actualised_total) AS total')->
            join('item_type_allocated_expense', 'item.id', 'item_type_allocated_expense.item_id')->
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
            $collection->whereRaw(DB::raw("YEAR(item_type_allocated_expense.effective_date) = {$year}"));
        }
        if ($month !== null) {
            $collection->whereRaw(DB::raw("MONTH(item_type_allocated_expense.effective_date) = {$month}"));
        }
        if (count($search_parameters) > 0) {
            foreach ($search_parameters as $field => $search_term) {
                $collection->where('item_type_allocated_expense.' . $field, 'LIKE', '%' . $search_term . '%');
            }
        }

        $collection = $this->includeUnpublished($collection, $include_unpublished);

        return $collection->get()->
            toArray();
    }

    /**
     * Return the summary for all items for the resources in the requested resource
     * type and category grouped by subcategory
     *
     * @param int $resource_type_id
     * @param int $category_id
     * @param boolean $include_unpublished
     *
     * @return array
     */
    public function subcategoriesSummary(
        int $resource_type_id,
        int $category_id,
        bool $include_unpublished = false
    ): array
    {
        $collection = $this->selectRaw('
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
            where("category.resource_type_id", "=", $resource_type_id)->
            where("resource_type.id", "=", $resource_type_id)->
            where("category.id", "=", $category_id);

        $collection = $this->includeUnpublished($collection, $include_unpublished);

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
     * @param boolean $include_unpublished
     *
     * @return array
     */
    public function subcategorySummary(
        int $resource_type_id,
        int $category_id,
        int $subcategory_id,
        bool $include_unpublished = false
    ): array
    {
        $collection = $this->selectRaw('
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
            where("category.resource_type_id", "=", $resource_type_id)->
            where("resource_type.id", "=", $resource_type_id)->
            where("category.id", "=", $category_id)->
            where('sub_category.id', '=', $subcategory_id);

        $collection = $this->includeUnpublished($collection, $include_unpublished);

        return $collection->groupBy("sub_category.id")->
            orderBy("name")->
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
                $sql->whereNull('item_type_allocated_expense.publish_after')->
                    orWhereRaw('item_type_allocated_expense.publish_after < NOW()');
            });
        }

        return $collection;
    }
}
