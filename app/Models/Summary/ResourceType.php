<?php

declare(strict_types=1);

namespace App\Models\Summary;

use App\Models\Utility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

/**
 * @mixin QueryBuilder
 *
 * @property int $id
 * @property int $public
 * @property string $name
 * @property string $description
 * @property string $data
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2025
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceType extends Model
{
    protected $table = 'resource_type';

    public function totalCount(
        array $viewable_resource_types = [],
        array $search_parameters = []
    ): array {
        $collection = $this
            ->selectRaw('COUNT(resource_type.id) AS total')
            ->selectRaw($this->lastUpdatedAggregateExpression());

        $collection = Utility::applyViewableResourceTypesClause(
            $collection,
            $viewable_resource_types
        );

        $collection = Utility::applySearchClauses($collection, $this->table, $search_parameters);

        return $collection
            ->get()
            ->toArray();
    }

    private function lastUpdatedAggregateExpression(): string
    {
        if (DB::getDriverName() === 'mysql') {
            return "GREATEST(
                    MAX(`{$this->table}`.`created_at`),
                    IFNULL(MAX(`{$this->table}`.`updated_at`), 0),
                    0
                ) AS last_updated";
        }

        return "MAX(
                    COALESCE({$this->table}.created_at, 0),
                    COALESCE({$this->table}.updated_at, 0),
                    0
                ) AS last_updated";
    }
}
