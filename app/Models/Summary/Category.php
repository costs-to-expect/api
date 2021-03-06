<?php
declare(strict_types=1);

namespace App\Models\Summary;

use App\Models\Clause;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * Category model
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2021
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Category extends Model
{
    protected $table = 'category';

    public function total(
        int $resource_type_id,
        array $viewable_resource_types,
        array $search_parameters = []
    ): array
    {
        $collection = $this
            ->selectRaw("COUNT(`{$this->table}`.`id`) AS total")
            ->selectRaw("
                (
                    SELECT 
                        GREATEST(
                            MAX(`{$this->table}`.`created_at`), 
                            IFNULL(MAX(`{$this->table}`.`updated_at`), 0),
                            0
                        )
                    FROM 
                        `{$this->table}` 
                    WHERE
                        `{$this->table}`.`resource_type_id` = ? 
                ) AS `last_updated`",
                [
                    $resource_type_id
                ]
            )
            ->join("resource_type", "category.resource_type_id", "resource_type.id")
            ->where('category.resource_type_id', '=', $resource_type_id);

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
