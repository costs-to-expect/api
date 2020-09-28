<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Currency extends Model
{
    protected $table = 'currency';

    protected $guarded = ['id', 'code', 'name', 'created_at', 'updated_at'];

    public function minimisedCollection(): array
    {
        return $this->select(
                "{$this->table}.id AS {$this->table}_id",
                "{$this->table}.code AS {$this->table}_code",
                "{$this->table}.name AS {$this->table}_name"
            )->
            get()->
            toArray();
    }

    public function paginatedCollection(
        int $offset = 0,
        int $limit = 10,
        array $search_parameters = [],
        array $sort_parameters = []
    ): array
    {
        $collection = $this->select(
            "{$this->table}.id AS {$this->table}_id",
            "{$this->table}.code AS {$this->table}_code",
            "{$this->table}.name AS {$this->table}_name",
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
            $collection->orderByDesc("{$this->table}.code");
        }

        $collection->offset($offset);
        $collection->limit($limit);

        return $collection->get()->toArray();
    }

    public function single(int $currency_id): array
    {
        $result = $this->select(
            "{$this->table}.id AS {$this->table}_id",
            "{$this->table}.code AS {$this->table}_code",
            "{$this->table}.name AS {$this->table}_name",
            "{$this->table}.created_at AS {$this->table}_created_at"
        );

        $result = $result->find($currency_id);

        if ($result !== null) {
            return $result->toArray();
        }

        return [];
    }

    public function totalCount(array $search_parameters = []): int
    {
        $collection = $this->select("{$this->table}.id");

        $collection = Clause::applySearch($collection, $this->table, $search_parameters);

        return $collection->count();
    }
}
