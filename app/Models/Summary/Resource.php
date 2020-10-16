<?php
declare(strict_types=1);

namespace App\Models\Summary;

use App\Models\Clause;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Resource model
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Resource extends Model
{
    protected $table = 'resource';

    /**
     * Return the total number of resources
     *
     * @param integer $resource_type_id
     * @param array $permitted_resource_types
     * @param boolean $include_public Include resources attached to public resource types
     * @param array $search_parameters
     *
     * @return integer
     */
    public function totalCount(
        int $resource_type_id,
        array $permitted_resource_types,
        bool $include_public,
        array $search_parameters = []
    ): int
    {
        $collection = $this->select("resource.id")->
            join('resource_type', 'resource.resource_type_id', 'resource_type.id')->
            where('resource_type.id', '=', $resource_type_id);

        $collection = Clause::applyPermittedResourceTypes(
            $collection,
            $permitted_resource_types,
            $include_public
        );

        $collection = Clause::applySearch($collection, $this->table, $search_parameters);

        return $collection->count();
    }
}
