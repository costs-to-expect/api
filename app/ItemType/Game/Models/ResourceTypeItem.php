<?php

declare(strict_types=1);

namespace App\ItemType\Game\Models;

use App\Models\Clause;
use Illuminate\Database\Eloquent\Model as LaravelModel;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeItem extends LaravelModel
{
    protected $table = 'item';

    protected $item_table = 'item_type_game';

    protected $guarded = ['id'];

    public function totalCount(
        int $resource_type_id,
        array $parameters_collection = [],
        array $search_parameters = [],
        array $filter_parameters = []
    ): int {
        $collection = $this
            ->from($this->table)
            ->join($this->item_table, 'item.id', $this->item_table . '.item_id')
            ->join('resource', 'item.resource_id', 'resource.id')
            ->join('resource_type', 'resource.resource_type_id', 'resource_type.id')
            ->where('resource_type.id', '=', $resource_type_id);

        if (array_key_exists('complete', $parameters_collection) === true) {
            $collection->where($this->item_table . '.complete', '=', 1);
        }

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
    ): array {
        $select_fields = [
            'resource.id AS resource_id',
            'resource.name AS resource_name',
            'resource.description AS resource_description',
            'item.id AS item_id',
            "{$this->item_table}.name AS item_name",
            "{$this->item_table}.description AS item_description",
            "{$this->item_table}.game AS item_game",
            "{$this->item_table}.statistics AS item_statistics",
            "category.id AS item_winner_id",
            "category.name AS item_winner_name",
            "{$this->item_table}.score AS item_score",
            "{$this->item_table}.complete AS item_complete",
            "{$this->item_table}.created_at AS item_created_at",
            "{$this->item_table}.updated_at AS item_updated_at"
        ];

        $collection = $this
            ->from('item')
            ->join($this->item_table, 'item.id', "{$this->item_table}.item_id")
            ->join('resource', 'item.resource_id', 'resource.id')
            ->join('resource_type', 'resource.resource_type_id', 'resource_type.id')
            ->leftJoin('category', $this->item_table . '.winner', 'category.id')
            ->where('resource_type.id', '=', $resource_type_id);

        if (array_key_exists('complete', $parameters_collection) === true) {
            $collection->where($this->item_table . '.complete', '=', 1);
        }

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
                        $collection->orderBy($this->item_table . '.created_at', $direction);
                        break;

                    default:
                        $collection->orderBy($this->item_table . '.' . $field, $direction);
                        break;
                }
            }
        } else {
            $collection->orderBy($this->item_table . '.created_at', 'desc');
        }

        return $collection
            ->offset($offset)
            ->limit($limit)
            ->select($select_fields)
            ->selectRaw(
                "
                (
                    SELECT 
                        GREATEST(
                            MAX(`{$this->item_table}`.`created_at`), 
                            IFNULL(MAX(`{$this->item_table}`.`updated_at`), 0),
                            0
                        )
                    FROM 
                        `{$this->item_table}`
                    INNER JOIN 
                        `item` ON 
                            {$this->item_table}.`item_id` = `{$this->table}`.`id`
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
