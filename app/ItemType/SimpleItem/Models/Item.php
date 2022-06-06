<?php
declare(strict_types=1);

namespace App\ItemType\SimpleItem\Models;

use App\Models\Clause;
use Illuminate\Database\Eloquent\Model as LaravelModel;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Item extends LaravelModel
{
    protected $table = 'item_type_simple_item';

    protected $guarded = ['id'];

    public $timestamps = false;

    public function instance(int $item_id): ?Item
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
     * @param Item $item
     * @param Item $item_type
     *
     * @return array
     */
    public function instanceToArray(LaravelModel $item, Item $item_type): array
    {
        return [
            'item_id' => $item->id, // This can come from item_type, no need to pass item into method
            'item_name' => $item_type->name,
            'item_description' => $item_type->description,
            'item_quantity' => $item_type->total,
            'item_created_at' => ($item_type->created_at !== null) ? $item_type->created_at->toDateTimeString() : null,
            'item_updated_at' => ($item_type->updated_at !== null) ? $item_type->updated_at->toDateTimeString() : null,
        ];
    }

    /**
     * @param integer $resource_type_id
     * @param integer $resource_id
     * @param integer $item_id
     * @param array $parameters
     *
     * @return array|null
     */
    public function single(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        array $parameters = []
    ): ?array
    {
        $fields = [
            'item.id AS item_id',
            "{$this->table}.name AS item_name",
            "{$this->table}.description AS item_description",
            "{$this->table}.quantity AS item_quantity",
            "{$this->table}.created_at AS item_created_at",
            "{$this->table}.updated_at AS item_updated_at"
        ];

        $result = $this->from('item')->
            join($this->table, 'item.id', "{$this->table}.item_id")->
            join('resource', 'item.resource_id', 'resource.id')->
            where('item.resource_id', '=', $resource_id)->
            where('resource.resource_type_id', '=', $resource_type_id)->
            where("{$this->table}.item_id", '=', $item_id)->
            where('item.id', '=', $item_id);

        $item = $result->select($fields)->first();

        if ($item !== null) {
            return $item->toArray();
        }

        return null;
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

        $collection = Clause::applySearch(
            $collection,
            $this->table,
            $search_parameters
        );
        $collection = Clause::applyFiltering(
            $collection,
            $this->table,
            $filter_parameters
        );

        return $collection->count();
    }

     public function paginatedCollection(
        int $resource_type_id,
        int $resource_id,
        int $offset = 0,
        int $limit = 10,
        array $search_parameters = [],
        array $filter_parameters = [],
        array $sort_parameters = []
    ): array
    {
        $select_fields = [
            'item.id AS item_id',
            "{$this->table}.name AS item_name",
            "{$this->table}.description AS item_description",
            "{$this->table}.quantity AS item_quantity",
            "{$this->table}.created_at AS item_created_at",
            "{$this->table}.updated_at AS item_updated_at"
        ];

        $collection = $this->from('item')->
            join($this->table, 'item.id', "{$this->table}.item_id")->
            join('resource', 'item.resource_id', 'resource.id')->
            where('resource_id', '=', $resource_id)->
            where('resource.resource_type_id', '=', $resource_type_id);

        $collection = Clause::applySearch(
            $collection,
            $this->table,
            $search_parameters
        );
        $collection = Clause::applyFiltering(
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

        return $collection
            ->select($select_fields)
            ->selectRaw("
                (
                    SELECT 
                        GREATEST(
                            MAX(`{$this->table}`.`created_at`), 
                            IFNULL(MAX(`{$this->table}`.`updated_at`), 0),
                            0
                        )
                    FROM 
                        `{$this->table}` 
                    JOIN 
                        `item` ON 
                            `{$this->table}`.`item_id` = `item`.`id`
                    WHERE
                        `item`.`resource_id` = ? 
                ) AS `last_updated`",
                [
                    $resource_id
                ]
            )
            ->get()
            ->toArray();
    }

    public function hasCategoryAssignments(int $item_id): bool
    {
        return false;
    }
}
