<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        $collection = $this->select(
                'resource.id AS resource_id',
                'resource.name AS resource_name',
                'item.id AS item_id',
                'item.description AS item_description',
                'item.effective_date AS item_effective_date',
                'item.total AS item_total',
                'item.percentage AS item_percentage',
                'item.actualised_total AS item_actualised_total',
                'item.created_at AS item_created_at'
            )->
            join('resource', 'item.resource_id', 'resource.id')->
            join('resource_type', 'resource.resource_type_id', 'resource_type.id')->
            where('resource_type.id', '=', $resource_type_id)->
            orderByDesc('item.effective_date')->
            orderByDesc('item.created_at')->
            offset($offset)->
            limit($limit);

        /*if (array_key_exists('year', $parameters_collection) === true &&
            $parameters_collection['year'] !== null) {
            $collection->whereRaw(\DB::raw("YEAR(item.effective_date) = '{$parameters_collection['year']}'"));
        }

        if (array_key_exists('month', $parameters_collection) === true &&
            $parameters_collection['month'] !== null) {
            $collection->whereRaw(\DB::raw("MONTH(item.effective_date) = '{$parameters_collection['month']}'"));
        }

        if (array_key_exists('category', $parameters_collection) === true &&
            $parameters_collection['category'] !== null) {
            $collection->join("item_category", "item_category.item_id", "item.id");
            $collection->where('item_category.category_id', '=', $parameters_collection['category']);
        }

        if (
            array_key_exists('category', $parameters_collection) === true &&
            $parameters_collection['category'] !== null &&
            array_key_exists('subcategory', $parameters_collection) === true &&
            $parameters_collection['subcategory'] !== null
        ) {
            $collection->join("item_sub_category", "item_sub_category.item_category_id", "item_category.id");
            $collection->where('item_sub_category.sub_category_id', '=', $parameters_collection['subcategory']);
        }*/

        return $collection->get()->toArray();
    }
}
