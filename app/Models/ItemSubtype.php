<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2021
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemSubtype extends Model
{
    protected $table = 'item_subtype';

    protected $guarded = ['id', 'name', 'description', 'created_at', 'updated_at'];

    public function minimisedCollection(int $item_type_id): array
    {
        return $this
            ->select(
                "{$this->table}.id AS {$this->table}_id",
                "{$this->table}.name AS {$this->table}_name",
                "{$this->table}.friendly_name AS {$this->table}_friendly_name",
                "{$this->table}.description AS {$this->table}_description"
            )
            ->where("{$this->table}.item_type_id", '=', $item_type_id)
            ->get()
            ->toArray();
    }

    public function paginatedCollection(
        int $item_type_id,
        int $offset = 0,
        int $limit = 10,
        array $search_parameters = [],
        array $sort_parameters = []
    ): array
    {
        $collection = $this->select(
            "{$this->table}.id AS {$this->table}_id",
            "{$this->table}.name AS {$this->table}_name",
            "{$this->table}.friendly_name AS {$this->table}_friendly_name",
            "{$this->table}.description AS {$this->table}_description",
            "{$this->table}.created_at AS {$this->table}_created_at"
        );

        $collection = Clause::applySearch($collection, $this->table, $search_parameters);

        if (count($sort_parameters) > 0) {
            foreach ($sort_parameters as $field => $direction) {
                switch ($field) {
                    case 'created':
                        $collection->orderBy("{$this->table}.created_at", $direction);
                        break;

                    default:
                        $collection->orderBy("{$this->table}." . $field, $direction);
                        break;
                }
            }
        } else {
            $collection->orderByDesc("{$this->table}.created_at");
        }

        $collection
            ->where("{$this->table}.item_type_id", '=', $item_type_id)
            ->offset($offset)
            ->limit($limit);

        return $collection
            ->get()
            ->toArray();
    }

    public function single(int $item_type_id, int $item_subtype_id): ?array
    {
        $result = $this
            ->select(
                "{$this->table}.id AS {$this->table}_id",
                "{$this->table}.name AS {$this->table}_name",
                "{$this->table}.friendly_name AS {$this->table}_friendly_name",
                "{$this->table}.description AS {$this->table}_description",
                "{$this->table}.created_at AS {$this->table}_created_at"
            )
            ->where("{$this->table}.item_type_id", '=', $item_type_id);

        $result = $result
            ->where($this->table . '.id', '=', $item_subtype_id)
            ->get()
            ->toArray();

        if (count($result) === 0) {
            return null;
        }

        return $result[0];
    }

    public function totalCount(int $item_type_id, array $search_parameters = []): int
    {
        $collection = $this
            ->select("{$this->table}.id")
            ->where("{$this->table}.item_type_id", '=', $item_type_id);

        $collection = Clause::applySearch($collection, $this->table, $search_parameters);

        return $collection->count();
    }
}
