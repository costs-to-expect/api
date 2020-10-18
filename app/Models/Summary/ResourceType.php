<?php
declare(strict_types=1);

namespace App\Models\Summary;

use App\Models\Clause;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Resource type model
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceType extends Model
{
    protected $table = 'resource_type';

    /**
     * Return the total number of resource types
     *
     * @param array $permitted_resource_types
     * @param boolean $include_public
     * @param array $search_parameters = []
     *
     * @return integer
     */
    public function totalCount(
        array $permitted_resource_types = [],
        bool $include_public = true,
        array $search_parameters = []
    ): int
    {
        $collection = $this->select("resource_type.id");

        $collection = Clause::applyPermittedResourceTypes(
            $collection,
            $permitted_resource_types,
            $include_public
        );

        $collection = Clause::applySearch($collection, $this->table, $search_parameters);

        return $collection->count();
    }
}
