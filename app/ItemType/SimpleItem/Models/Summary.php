<?php

declare(strict_types=1);

namespace App\ItemType\SimpleItem\Models;

use App\Models\Clause;
use Illuminate\Database\Eloquent\Model as LaravelModel;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @mixin QueryBuilder
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Summary extends LaravelModel
{
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $table = 'item';
    protected $sub_table = 'item_type_simple_item';

    public function filteredSummary(
        int $resource_type_id,
        int $resource_id,
        array $parameters = [],
        array $search_parameters = [],
        array $filter_parameters = []
    ): array {
        $collection = $this->
            selectRaw("
                SUM({$this->sub_table}.quantity) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count
            ")
            ->selectRaw(
                "
                (
                    SELECT 
                        GREATEST(
                            MAX(`{$this->sub_table}`.`created_at`), 
                            IFNULL(MAX(`{$this->sub_table}`.`updated_at`), 0),
                            0
                        )
                    FROM 
                        `{$this->sub_table}` 
                    JOIN 
                        `item` ON 
                            `{$this->sub_table}`.`item_id` = `{$this->table}`.`id`
                    WHERE
                        `item`.`resource_id` = ? 
                ) AS `last_updated`",
                [
                    $resource_id
                ]
            )
            ->join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")
            ->join("resource", "resource.id", "item.resource_id")
            ->join("resource_type", "resource_type.id", "resource.resource_type_id")
            ->where("resource_type.id", "=", $resource_type_id)
            ->where("resource.id", "=", $resource_id);

        $collection = Clause::applySearch(
            $collection,
            $this->sub_table,
            $search_parameters
        );
        $collection = Clause::applyFiltering(
            $collection,
            $this->sub_table,
            $filter_parameters
        );

        return $collection->get()->
            toArray();
    }

    /**
     * Return the total summary for all items
     *
     * @param int $resource_type_id
     * @param int $resource_id
     * @param array $parameters
     *
     * @return array
     */
    public function summary(
        int $resource_type_id,
        int $resource_id,
        array $parameters = []
    ): array {
        $collection = $this->selectRaw("
                SUM({$this->sub_table}.quantity) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count
            ")
            ->selectRaw(
                "
                (
                    SELECT 
                        GREATEST(
                            MAX(`{$this->sub_table}`.`created_at`), 
                            IFNULL(MAX(`{$this->sub_table}`.`updated_at`), 0),
                            0
                        )
                    FROM 
                        `{$this->sub_table}` 
                    JOIN 
                        `item` ON 
                            `{$this->sub_table}`.`item_id` = `{$this->table}`.`id`
                    WHERE
                        `item`.`resource_id` = ? 
                ) AS `last_updated`",
                [
                    $resource_id
                ]
            )
            ->join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")
            ->join('resource', 'item.resource_id', 'resource.id')
            ->where('resource_id', '=', $resource_id)
            ->where('resource.resource_type_id', '=', $resource_type_id);

        return $collection
            ->get()
            ->toArray();
    }
}
