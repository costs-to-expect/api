<?php
declare(strict_types=1);

namespace App\ItemType\Game;

use App\Models\Clause;
use Illuminate\Database\Eloquent\Model as LaravelModel;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeModel extends LaravelModel
{
    protected $table = 'item_type_game';

    protected $guarded = ['id'];

    public function totalCount(
        int $resource_type_id,
        array $parameters_collection = [],
        array $search_parameters = [],
        array $filter_parameters = []
    ): int
    {
        $collection = $this
            ->from('item')
            ->join($this->table, 'item.id', $this->table . '.item_id')
            ->join('resource', 'item.resource_id', 'resource.id')
            ->join('resource_type', 'resource.resource_type_id', 'resource_type.id')
            ->where('resource_type.id', '=', $resource_type_id);

        if (array_key_exists('complete', $parameters_collection) === true) {
            $collection->where($this->table . '.complete', '=', 1);
        }

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

        return $collection->count('item.id');
    }

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
            "{$this->table}.name AS item_name",
            "{$this->table}.description AS item_description",
            "{$this->table}.game AS item_game",
            "{$this->table}.statistics AS item_statistics",
            "category.id AS item_winner_id",
            "category.name AS item_winner_name",
            "{$this->table}.score AS item_score",
            "{$this->table}.complete AS item_complete",
            "{$this->table}.created_at AS item_created_at",
            "{$this->table}.updated_at AS item_updated_at"
        ];

        $collection = $this
            ->from('item')
            ->join($this->table, 'item.id', "{$this->table}.item_id")
            ->join('resource', 'item.resource_id', 'resource.id')
            ->join('resource_type', 'resource.resource_type_id', 'resource_type.id')
            ->leftJoin('category', $this->table . '.winner', 'category.id')
            ->where('resource_type.id', '=', $resource_type_id);

        if (array_key_exists('complete', $parameters_collection) === true) {
            $collection->where($this->table . '.complete', '=', 1);
        }

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

                    default:
                        $collection->orderBy('item.' . $field, $direction);
                        break;
                }
            }
        } else {
            $collection->orderBy('item.created_at', 'desc');
        }

        return $collection
            ->offset($offset)
            ->limit($limit)
            ->select($select_fields)
            ->selectRaw("
                (
                    SELECT 
                        GREATEST(
                            MAX(`{$this->item_table}`.`created_at`), 
                            IFNULL(MAX(`{$this->item_table}`.`updated_at`), 0)
                        )
                    FROM 
                        `{$this->item_table}`
                    INNER JOIN 
                        `item` ON 
                            {$this->item_table}.`id` = `{$this->table}`.`id`
                    INNER JOIN 
                        `resource` ON 
                            `item`.`resource_id` = `resource`.`id`
                    WHERE
                        `resource`.`resource_type_id` = ? 
                ) AS `last_updated`",
                [
                    $resource_type_id
                ]
            )
            ->get()
            ->toArray();
    }
}
