<?php
declare(strict_types=1);

namespace App\Models;

use App\Utilities\General;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Item model, fetches data by resource type
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
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
     *
     * @return integer
     */
    public function totalCount(int $resource_type_id, array $parameters_collection = []): int
    {
        $collection = $this->join('resource', 'item.resource_id', 'resource.id')->
            join('resource_type', 'resource.resource_type_id', 'resource_type.id')->
            where('resource_type.id', '=', $resource_type_id);

        return count($collection->get());
    }

    /**
     * Return the pagination collection for all the items assigned to the
     * resources for a resource group
     *
     * @param int $resource_type_id
     * @param int $offset
     * @param int $limit
     * @param array $parameters_collection
     *
     * @return array
     */
    public function paginatedCollection(
        int $resource_type_id,
        int $offset = 0,
        int $limit = 10,
        array $parameters_collection = []
    ): array
    {
        $select_fields = [
            'resource.id AS resource_id',
            'resource.name AS resource_name',
            'resource.description AS resource_description',
            'item.id AS item_id',
            'item.description AS item_description',
            'item.effective_date AS item_effective_date',
            'item.total AS item_total',
            'item.percentage AS item_percentage',
            'item.actualised_total AS item_actualised_total',
            'item.created_at AS item_created_at'
        ];

        $collection = $this->join('resource', 'item.resource_id', 'resource.id')->
            join('resource_type', 'resource.resource_type_id', 'resource_type.id')->
            where('resource_type.id', '=', $resource_type_id)->
            orderByDesc('item.effective_date')->
            orderByDesc('item.created_at')->
            offset($offset)->
            limit($limit);

        if (
            array_key_exists('include-categories', $parameters_collection) === true &&
            General::booleanValue($parameters_collection['include-categories']) === true
        ) {
            $collection->leftJoin('item_category', 'item.id', 'item_category.item_id')->
                leftJoin('category', 'item_category.category_id', 'category.id');

            $select_fields[] = 'category.id AS category_id';
            $select_fields[] = 'category.name AS category_name';
            $select_fields[] = 'category.description AS category_description';

            if (
                array_key_exists('include-subcategories', $parameters_collection) === true &&
                General::booleanValue($parameters_collection['include-subcategories']) === true
            ) {
                $collection->leftJoin('item_sub_category', 'item_category.id', 'item_sub_category.item_category_id')->
                    leftJoin('sub_category', 'item_sub_category.sub_category_id', 'sub_category.id');

                $select_fields[] = 'sub_category.id AS subcategory_id';
                $select_fields[] = 'sub_category.name AS subcategory_name';
                $select_fields[] = 'sub_category.description AS subcategory_description';
            }
        }

        $collection->select($select_fields);

        return $collection->get()->toArray();
    }

    /**
     * Return the summary for all items for the resources in the requested resource type
     *
     * @param int $resource_type_id
     *
     * @return array
     */
    public function summary(int $resource_type_id): array
    {
        return $this->selectRaw('sum(item.actualised_total) AS actualised_total')->
            join('resource', 'item.resource_id', 'resource.id')->
            join('resource_type', 'resource.resource_type_id', 'resource_type.id')->
            where('resource_type.id', '=', $resource_type_id)->
            get()->
            toArray();
    }

    /**
     * Return the summary for all items for the resources in the requested resource
     * type grouped by year
     *
     * @param int $resource_type_id

     * @return array
     */
    public function yearsSummary(int $resource_type_id): array
    {
        return $this->selectRaw("YEAR(item.effective_date) as year, SUM(item.actualised_total) AS total")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            where("resource_type.id", "=", $resource_type_id)->
            groupBy("year")->
            orderBy("year")->
            get()->
            toArray();
    }

    /**
     * Return the summary for all items for the resources in the requested resource
     * type grouped by month for the requested year
     *
     * @param integer $year
     * @param integer $resource_type_id

     * @return array
     */
    public function monthsSummary(int $resource_type_id, int $year): array
    {
        return $this->selectRaw("MONTH(item.effective_date) as month, SUM(item.actualised_total) AS total")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where(DB::raw('YEAR(item.effective_date)'), '=', $year)->
            groupBy("month")->
            orderBy("month")->
            get()->
            toArray();
    }

    /**
     * Return the summary for all items for the resources in the requested resource
     * type for a specific year and month
     *
     * @param integer $year
     * @param integer $month
     * @param int $resource_type_id

     * @return array
     */
    public function monthSummary(int $resource_type_id, int $year, int $month): array
    {
        return $this->selectRaw("MONTH(item.effective_date) as month, SUM(item.actualised_total) AS total")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where(DB::raw('YEAR(item.effective_date)'), '=', $year)->
            where(DB::raw('MONTH(item.effective_date)'), '=', $month)->
            groupBy("month")->
            orderBy("month")->
            get()->
            toArray();
    }

    /**
     * Return the summary for all items for the resources in the requested resource
     * type for a specific year
     *
     * @param integer $year
     * @param integer $resource_type_id

     * @return array
     */
    public function yearSummary(int $resource_type_id, int $year): array
    {
        return $this->selectRaw("YEAR(item.effective_date) as year, SUM(item.actualised_total) AS total")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where(DB::raw('YEAR(item.effective_date)'), '=', $year)->
            groupBy("year")->
            orderBy("year")->
            get()->
            toArray();
    }

    /**
     * Return the summary for all items for the resources in the requested resource
     * type grouped by category
     *
     * @param integer $resource_type_id
     *
     * @return array
     */
    public function categoriesSummary(int $resource_type_id): array
    {
        return $this->selectRaw("category.id, category.name AS name, SUM(item.actualised_total) AS total")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            join("item_category", "item_category.item_id", "item.id")->
            join("category", "category.id", "item_category.category_id")->
            where("category.resource_type_id", "=", $resource_type_id)->
            where("resource_type.id", "=", $resource_type_id)->
            groupBy("category.id")->
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
     *
     * @return array
     */
    public function categorySummary(int $resource_type_id, int $category_id): array
    {
        return $this->selectRaw("category.id, category.name AS name, SUM(item.actualised_total) AS total")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            join("item_category", "item_category.item_id", "item.id")->
            join("category", "category.id", "item_category.category_id")->
            where("category.resource_type_id", "=", $resource_type_id)->
            where("resource_type.id", "=", $resource_type_id)->
            where("category.id", '=', $category_id)->
            groupBy("category.id")->
            orderBy("name")->
            get()->
            toArray();
    }

    /**
     * Return the summary for all items for the resources in the requested resource
     * type and category grouped by subcategory
     *
     * @param int $resource_type_id
     * @param int $category_id
     *
     * @return array
     */
    public function subcategoriesSummary(int $resource_type_id, int $category_id): array
    {
        return $this->selectRaw("sub_category.id, sub_category.name AS name, SUM(item.actualised_total) AS total")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            join("item_category", "item_category.item_id", "item.id")->
            join("item_sub_category", "item_sub_category.item_category_id", "item_category.id")->
            join("category", "category.id", "item_category.category_id")->
            join("sub_category", "sub_category.id", "item_sub_category.sub_category_id")->
            where("category.resource_type_id", "=", $resource_type_id)->
            where("resource_type.id", "=", $resource_type_id)->
            where("category.id", "=", $category_id)->
            groupBy("sub_category.id")->
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
     *
     * @return array
     */
    public function subcategorySummary(int $resource_type_id, int $category_id, int $subcategory_id): array
    {
        return $this->selectRaw("sub_category.id, sub_category.name AS name, SUM(item.actualised_total) AS total")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            join("item_category", "item_category.item_id", "item.id")->
            join("item_sub_category", "item_sub_category.item_category_id", "item_category.id")->
            join("category", "category.id", "item_category.category_id")->
            join("sub_category", "sub_category.id", "item_sub_category.sub_category_id")->
            where("category.resource_type_id", "=", $resource_type_id)->
            where("resource_type.id", "=", $resource_type_id)->
            where("category.id", "=", $category_id)->
            where('sub_category.id', '=', $subcategory_id)->
            groupBy("sub_category.id")->
            orderBy("name")->
            get()->
            toArray();
    }
}