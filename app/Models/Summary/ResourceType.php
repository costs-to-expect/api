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

    public function totalCount(
        array $viewable_resource_types = [],
        array $search_parameters = []
    ): int
    {
        $collection = $this->select("resource_type.id");

        $collection = Clause::applyViewableResourceTypes(
            $collection,
            $viewable_resource_types
        );

        $collection = Clause::applySearch($collection, $this->table, $search_parameters);

        return $collection->count();
    }
}
