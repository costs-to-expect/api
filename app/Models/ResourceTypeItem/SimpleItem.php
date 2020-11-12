<?php
declare(strict_types=1);

namespace App\Models\ResourceTypeItem;

use App\Models\Clause;
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
     * @param array $search_parameters
     * @param array $filter_parameters
     *
     * @return integer
     */
    public function totalCount(
        int $resource_type_id,
        array $parameters_collection = [],
        array $search_parameters = [],
        array $filter_parameters = []
    ): int
    {
        $collection = $this->join($this->item_table, 'item.id', "{$this->item_table}.item_id")->
            join('resource', 'item.resource_id', 'resource.id')->
            join('resource_type', 'resource.resource_type_id', 'resource_type.id')->
            where('resource_type.id', '=', $resource_type_id);

        $collection = Clause::applySearch(
            $collection,
            $this->item_table,
            $search_parameters
        );

        $collection = Clause::applyFiltering(
            $collection,
            $this->item_table,
            $filter_parameters
        );

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
     * @param array $search_parameters
     * @param array $filter_parameters
     * @param array $sort_parameters
     *
     * @return array
     */
    public function paginatedCollection(
        int $resource_type_id,
        int $offset = 0,
        int $limit = 10,
        array $parameters_collection = [],
        array $search_parameters = [],
        array $filter_parameters = [],
        array $sort_parameters = []
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
            "{$this->item_table}.created_at AS item_created_at",
            "{$this->item_table}.updated_at AS item_updated_at"
        ];

        $collection = $this->join($this->item_table, 'item.id', "{$this->item_table}.item_id")->
            join('resource', 'item.resource_id', 'resource.id')->
            join('resource_type', 'resource.resource_type_id', 'resource_type.id')->
            where('resource_type.id', '=', $resource_type_id);

        $collection = Clause::applySearch(
            $collection,
            $this->item_table,
            $search_parameters
        );

        $collection = Clause::applyFiltering(
            $collection,
            $this->item_table,
            $filter_parameters
        );

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
}
