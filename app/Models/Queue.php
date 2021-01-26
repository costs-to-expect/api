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
class Queue extends Model
{
    protected $table = 'jobs';

    protected $guarded = ['id', 'queue', 'payload', 'attempts', 'reserved_at', 'available_at', 'created_at'];

    public function paginatedCollection(
        int $offset = 0,
        int $limit = 10
    ): array
    {
        $collection = $this
            ->select(
                "{$this->table}.id AS {$this->table}_id",
                "{$this->table}.queue AS {$this->table}_queue",
                "{$this->table}.created_at AS {$this->table}_created_at"
            )
            ->orderByDesc("{$this->table}.created_at")
            ->offset($offset)
            ->limit($limit);

        return $collection->get()->toArray();
    }

    public function single(int $currency_id): ?array
    {
        $result = $this
            ->select(
                "{$this->table}.id AS {$this->table}_id",
                "{$this->table}.queue AS {$this->table}_queue",
                "{$this->table}.created_at AS {$this->table}_created_at"
            );

        $result = $result
            ->where($this->table . '.id', '=', $currency_id)
            ->get()
            ->toArray();

        if (count($result) === 0) {
            return null;
        }

        return $result[0];
    }

    public function totalCount(): int
    {
        $collection = $this->select("{$this->table}.id");

        return $collection->count();
    }
}
