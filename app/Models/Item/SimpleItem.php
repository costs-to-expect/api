<?php
declare(strict_types=1);

namespace App\Models\Item;

use App\Interfaces\Item\IModel;
use App\Utilities\Model as ModelUtility;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * Item type model
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SimpleItem extends Model implements IModel
{
    protected $table = 'item_type_simple_item';

    protected $guarded = ['id', 'updated_at', 'created_at'];

    public function instance(int $item_id): ?Model
    {
        return $this->where('item_id', '=', $item_id)->
            select(
                "{$this->table}.id"
            )->
            first();
    }

    /**
     * Convert the model instance to an array for use with the item transformer
     *
     * @param Model $item
     * @param Model $item_type
     *
     * @return array
     */
    public function instanceToArray(Model $item, Model $item_type): array
    {
        return [
            'item_id' => $item->id,
            'item_name' => $item_type->name,
            'item_description' => $item_type->description,
            'item_quantity' => $item_type->total,
            'item_created_at' => $item->created_at->toDateTimeString()
        ];
    }

    /**
     * @param integer $resource_type_id
     * @param integer $resource_id
     * @param integer $item_id
     *
     * @return array|null
     */
    public function single(
        int $resource_type_id,
        int $resource_id,
        int $item_id
    ): ?array
    {
        $result = $this->from('item')->
            join($this->table, 'item.id', "{$this->table}.item_id")->
            join('resource', 'item.resource_id', 'resource.id')->
            where('item.resource_id', '=', $resource_id)->
            where('resource.resource_type_id', '=', $resource_type_id)->
            where("{$this->table}.item_id", '=', $item_id)->
            where('item.id', '=', $item_id)->
            select(
                'item.id AS item_id',
                "{$this->table}.name AS item_name",
                "{$this->table}.description AS item_description",
                "{$this->table}.quantity AS item_quantity",
                'item.created_at AS item_created_at'
            )->
            first();

        if ($result !== null) {
            return $result->toArray();
        } else {
            return null;
        }
    }

    /**
     * Return the total count for the given request, similar to the collection
     * method just without the sorting and only returning a count
     *
     * @param integer $resource_type_id
     * @param integer $resource_id
     * @param array $parameters
     * @param array $search_parameters
     * @param array $filter_parameters
     *
     * @return integer
     */
    public function totalCount(
        int $resource_type_id,
        int $resource_id,
        array $parameters = [],
        array $search_parameters = [],
        array $filter_parameters = []
    ): int
    {
        $collection = $this->from('item')->
            join($this->table, 'item.id', $this->table . '.item_id')->
            join('resource', 'item.resource_id', 'resource.id')->
            where('resource_id', '=', $resource_id)->
            where('resource.resource_type_id', '=', $resource_type_id);

        $collection = ModelUtility::applySearch(
            $collection,
            $this->table,
            $search_parameters
        );
        $collection = ModelUtility::applyFiltering(
            $collection,
            $this->table,
            $filter_parameters
        );

        return $collection->count();
    }

    /**
     * Return the results for the given request based on the supplied parameters
     *
     * @param integer $resource_type_id
     * @param integer $resource_id
     * @param integer $offset
     * @param integer $limit
     * @param array $parameters
     * @param array $sort_parameters
     * @param array $search_parameters
     * @param array $filter_parameters
     *
     * @return array
     */
    public function paginatedCollection(
        int $resource_type_id,
        int $resource_id,
        int $offset = 0,
        int $limit = 10,
        array $parameters = [],
        array $sort_parameters = [],
        array $search_parameters = [],
        array $filter_parameters = []
    ): array
    {
        $select_fields = [
            'item.id AS item_id',
            "{$this->table}.name AS item_name",
            "{$this->table}.description AS item_description",
            "{$this->table}.quantity AS item_quantity",
            'item.created_at AS item_created_at'
        ];

        $collection = $this->from('item')->
            join($this->table, 'item.id', "{$this->table}.item_id")->
            join('resource', 'item.resource_id', 'resource.id')->
            where('resource_id', '=', $resource_id)->
            where('resource.resource_type_id', '=', $resource_type_id);

        $collection = ModelUtility::applySearch(
            $collection,
            $this->table,
            $search_parameters
        );
        $collection = ModelUtility::applyFiltering(
            $collection,
            $this->table,
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
                        $collection->orderBy($this->table . '.' . $field, $direction);
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

        return $collection->select($select_fields)->
            get()->
            toArray();
    }
}
