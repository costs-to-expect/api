<?php
declare(strict_types=1);

namespace App\Models\Item;

use App\Interfaces\Item\IModel;
use App\Models\Clause;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Game extends Model implements IModel
{
    protected $table = 'item_type_game';

    protected $guarded = ['id'];

    public $timestamps = false;

    public function instance(int $item_id): ?Model
    {
        return $this->where('item_id', '=', $item_id)->
            select(
                "{$this->table}.id"
            )->
            first();
    }

    public function instanceToArray(Model $item, Model $item_type): array
    {
        return [
            'item_id' => $item->id,
            'item_name' => $item_type->name,
            'item_description' => $item_type->description,
            'item_game' => $item_type->game,
            'item_statistics' => $item_type->statistics,
            'item_winner' => $item_type->winner,
            'item_score' => $item_type->score,
            'item_complete' => $item_type->complete,
            'item_created_at' => ($item_type->created_at !== null) ? $item_type->created_at->toDateTimeString() : null,
            'item_updated_at' => ($item_type->updated_at !== null) ? $item_type->updated_at->toDateTimeString() : null,
        ];
    }

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
            "{$this->table}.game AS item_game",
            "{$this->table}.statistics AS item_statistics",
            "{$this->table}.winner AS item_winner",
            "{$this->table}.score AS item_score",
            "{$this->table}.complete AS item_complete",
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
        array $parameters = [],
        array $search_parameters = [],
        array $filter_parameters = [],
        array $sort_parameters = []
    ): array
    {
        $select_fields = [
            'item.id AS item_id',
            "{$this->table}.name AS item_name",
            "{$this->table}.description AS item_description",
            "{$this->table}.game AS item_game",
            "{$this->table}.statistics AS item_statistics",
            "{$this->table}.winner AS item_winner",
            "{$this->table}.score AS item_score",
            "{$this->table}.complete AS item_complete",
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
            ->get()
            ->toArray();
    }

    public function hasCategoryAssignments(int $item_id): bool
    {
        // Do something here

        return false;
    }
}
