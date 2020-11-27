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
    ): array
    {
        $collection = $this
            ->selectRaw('COUNT(resource_type.id) AS total')
            ->selectRaw(
                "GREATEST(
                    MAX(`{$this->table}`.`created_at`),
                    IFNULL(MAX(`{$this->table}`.`updated_at`), 0)
                ) AS last_updated"
            )
            ->groupBy("{$this->table}.id");

        $collection = Clause::applyViewableResourceTypes(
            $collection,
            $viewable_resource_types
        );

        $collection = Clause::applySearch($collection, $this->table, $search_parameters);

        return $collection
            ->get()
            ->toArray();
    }
}
