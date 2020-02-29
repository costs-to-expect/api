<?php
declare(strict_types=1);

namespace App\Models\ResourceTypeItemType;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Item model when fetching data by resource type
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SimpleItem extends Model
{
    protected $table = 'item';

    protected $item_table = 'item_type_simple_item';

    protected $guarded = ['id', 'created_at', 'updated_at'];

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
        $collection = $this->join($this->item_table, 'item.id', "{$this->item_table}.item_id")->
            join('resource', 'item.resource_id', 'resource.id')->
            join('resource_type', 'resource.resource_type_id', 'resource_type.id')->
            where('resource_type.id', '=', $resource_type_id);

        if (count($search_conditions) > 0) {
            foreach ($search_conditions as $field => $search_term) {
                $collection->where($this->item_table . '.' . $field, 'LIKE', '%' . $search_term . '%');
            }
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
            "{$this->item_table}.name AS item_name",
            "{$this->item_table}.description AS item_description",
            "{$this->item_table}.quantity AS item_quantity",
            'item.created_at AS item_created_at'
        ];

        $collection = $this->join($this->item_table, 'item.id', "{$this->item_table}.item_id")->
            join('resource', 'item.resource_id', 'resource.id')->
            join('resource_type', 'resource.resource_type_id', 'resource_type.id')->
            where('resource_type.id', '=', $resource_type_id);

        if (count($search_conditions) > 0) {
            foreach ($search_conditions as $field => $search_term) {
                $collection->where($this->item_table . '.' . $field, 'LIKE', '%' . $search_term . '%');
            }
        }

        if (count($sort_parameters) > 0) {
            foreach ($sort_parameters as $field => $direction) {
                switch ($field) {
                    case 'created':
                        $collection->orderBy('item.created_at', $direction);
                        break;
                    case 'description':
                    case 'name':
                    case 'quantity':
                        $collection->orderBy($this->item_table . '.' . $field, $direction);
                        break;

                    default:
                        $collection->orderBy('item.' . $field, $direction);
                        break;
                }
            }
        } else {
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
     *
     * @return array
     */
    public function summary(int $resource_type_id): array
    {
        $collection = $this->selectRaw("sum({$this->item_table}.quantity) AS total")->
            join($this->item_table, 'item.id', "{$this->item_table}.item_id")->
            join('resource', 'item.resource_id', 'resource.id')->
            join('resource_type', 'resource.resource_type_id', 'resource_type.id')->
            where('resource_type.id', '=', $resource_type_id);

        return $collection->
            get()->
            toArray();
    }

    /**
     * Return the summary for all items for the resources in the requested resource
     * type grouped by resource
     *
     * @param int $resource_type_id
     *
     * @return array
     */
    public function resourcesSummary(int $resource_type_id): array
    {
        $collection = $this->selectRaw("
                resource.id AS id, 
                resource.name AS `name`, 
                SUM({$this->item_table}.total) AS total"
            )->
            join($this->item_table, 'item.id', "{$this->item_table}.item_id")->
            join('resource', 'item.resource_id', 'resource.id')->
            join('resource_type', 'resource.resource_type_id', 'resource_type.id')->
            where('resource_type.id', '=', $resource_type_id);

        return $collection->groupBy('resource.id')->
            orderBy('name')->
            get()->
            toArray();
    }
}
