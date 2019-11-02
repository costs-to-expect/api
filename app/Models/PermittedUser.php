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
}
