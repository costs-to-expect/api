<?php

declare(strict_types=1);

namespace App\Models\Summary;

use App\Models\Utility;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin QueryBuilder
 *
 * @property int $id
 * @property int $resource_type_id
 * @property string $name
 * @property string $description
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Category extends Model
{
    protected $table = 'category';

    public function total(
        int $resource_type_id,
        array $viewable_resource_types,
        array $search_parameters = []
    ): array {
        $collection = $this
            ->selectRaw("COUNT(`{$this->table}`.`id`) AS total")
            ->selectRaw(
                "
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

        $collection = Utility::applyViewableResourceTypesClause(
            $collection,
            $viewable_resource_types
        );

        $collection = Utility::applySearchClauses($collection, $this->table, $search_parameters);

        return $collection->get()
            ->toArray();
    }
}
