<?php
declare(strict_types=1);

namespace App\Models\Summary;

use App\Models\Clause;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * Sub category model
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2021
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Subcategory extends Model
{
    protected $table = 'sub_category';

    public function totalCount(
        int $resource_type_id,
        int $category_id,
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
                        `{$this->table}`.`category_id` = ? 
                ) AS `last_updated`",
                [
                    $category_id
                ]
            )
            ->join('category', 'sub_category.category_id', 'category.id')
            ->where('sub_category.category_id', '=', $category_id)
            ->where('category.resource_type_id', '=', $resource_type_id);

        $collection = Clause::applySearch($collection, $this->table, $search_parameters);

        return $collection
            ->get()
            ->toArray();
    }
}
