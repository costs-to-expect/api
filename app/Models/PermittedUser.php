<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @mixin QueryBuilder
 *
 * @property int $id
 * @property int $resource_type_id
 * @property int $user_id
 * @property int $added_by
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class PermittedUser extends Model
{
    protected $table = 'permitted_user';

    protected $guarded = ['id'];

    public function instance(int $resource_type_id, int $user_id): ?PermittedUser
    {
        return $this->where('resource_type_id', '=', $resource_type_id)->
            where('user_id', '=', $user_id)->
            first();
    }

    public function totalCount(
        int $resource_type_id,
        array $search_parameters = []
    ): int
    {
        $collection = $this->select("permitted_user.id")->
            join('users', 'permitted_user.user_id', 'users.id')->
            where('permitted_user.resource_type_id', '=', $resource_type_id);

        $collection = Clause::applySearch($collection, 'users', $search_parameters);

        return $collection->count('permitted_user.id');
    }

    public function paginatedCollection(
        int $resource_type_id,
        int $offset = 0,
        int $limit = 10,
        array $search_parameters = [],
        array $sort_parameters = []
    ): array
    {
        $collection = $this->select(
                'permitted_user.id AS permitted_user_id',
                'users.name AS permitted_user_name',
                'users.email AS permitted_user_email',
                'permitted_user.created_at AS permitted_user_created_at'
            )->
            join('users', 'permitted_user.user_id', 'users.id')->
            where('permitted_user.resource_type_id', '=', $resource_type_id);

        $collection = Clause::applySearch($collection, 'users', $search_parameters);

        if (count($sort_parameters) > 0) {
            foreach ($sort_parameters as $field => $direction) {
                switch ($field) {
                    case 'created':
                        $collection->orderBy($this->table . '.created_at', $direction);
                        break;

                    default:
                        $collection->orderBy('users.' . $field, $direction);
                        break;
                }
            }
        } else {
            $collection->orderBy($this->table . '.created_at', 'desc');
        }

        return $collection->offset($offset)
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function single(int $resource_type_id, int $permitted_user_id): ?array
    {
        $result = $this->select(
                'permitted_user.id AS permitted_user_id',
                'users.name AS permitted_user_name',
                'users.email AS permitted_user_email',
                'permitted_user.created_at AS permitted_user_created_at'
            )
            ->join('users', 'permitted_user.user_id', 'users.id')
            ->where('permitted_user.resource_type_id', '=', $resource_type_id)
            ->where('permitted_user.id', '=', $permitted_user_id)
            ->get()
            ->toArray();

        if (count($result) === 0) {
            return null;
        }

        return $result[0];
    }
}
