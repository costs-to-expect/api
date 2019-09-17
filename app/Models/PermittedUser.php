<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

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
     * @return PermittedUser|null
     */
    public function instance(int $resource_type_id, int $user_id): ?PermittedUser
    {
        return $this->where('resource_type_id', '=', $resource_type_id)->
            where('user_id', '=', $user_id)->
            first();
    }

    /**
     * Fetch all the resource types the user has access to
     *
     * @param integer $user_id
     *
     * @return array
     */
    public function permittedResourceTypes(int $user_id): array
    {
        $permitted = [];

        $results = $this->where('user_id', '=', $user_id)->
            select('resource_type_id')->
            get()->
            toArray();

        foreach ($results as $row) {
            $permitted[] = $row['resource_type_id'];
        }

        return $permitted;
    }
}
