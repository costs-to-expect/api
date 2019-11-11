<?php
declare(strict_types=1);

namespace App\Models;

use App\Utilities\Model as ModelUtility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Error log
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class PermittedUser extends Model
{
    protected $table = 'permitted_user';

    protected $guarded = ['id'];

    /**
     * Return an instance of a permitted user
     *
     * @param integer $resource_type_id
     * @param integer $user_id
     *
     * @return ResourceTypeAccess|null
     */
    public function instance(int $resource_type_id, int $user_id): ?PermittedUser
    {
        return $this->where('resource_type_id', '=', $resource_type_id)->
            where('user_id', '=', $user_id)->
            first();
    }

    /**
     * Return the total number of permitted users for the resource type
     *
     * @param integer $resource_type_id
     * @param array $search_parameters
     *
     * @return integer
     */
    public function totalCount(
        int $resource_type_id,
        array $search_parameters = []
    ): int
    {
        $collection = $this->select("permitted_user.id")->
            join('users', 'permitted_user.user_id', 'users.id')->
            where('permitted_user.resource_type_id', '=', $resource_type_id);

        $collection = ModelUtility::applySearch($collection, 'users', $search_parameters);

        return $collection->count('permitted_user.id');
    }

    /**
     * Return the permitted users based on the given conditions
     *
     * @param integer $resource_type_id
     * @param integer $offset
     * @param integer $limit
     * @param array $search_parameters
     * @param array $sort_parameters
     *
     * @return array
     */
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

        $collection = ModelUtility::applySearch($collection, 'users', $search_parameters);

        if (count($sort_parameters) > 0) {
            foreach ($sort_parameters as $field => $direction) {
                switch ($field) {
                    case 'created':
                        $collection->orderBy('permitted_user.created_at', $direction);
                        break;

                    default:
                        $collection->orderBy('users.' . $field, $direction);
                        break;
                }
            }
        } else {
            $collection->orderBy('permitted_user.created_at', 'desc');
        }

        return $collection->offset($offset)->limit($limit)->get()->toArray();
    }
}
