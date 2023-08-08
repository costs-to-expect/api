<?php

declare(strict_types=1);

namespace App\ItemType\Game\Models;

use App\Models\Utility;
use Illuminate\Database\Eloquent\Model as LaravelModel;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @mixin QueryBuilder
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Summary extends LaravelModel
{
    protected $guarded = ['id'];
    protected $table = 'item';
    protected $sub_table = 'item_type_game';

    public function filteredSummary(
        int $resource_type_id,
        int $resource_id,
        array $parameters = [],
        array $search_parameters = [],
        array $filter_parameters = []
    ): array {
        $collection = $this
            ->selectRaw("
                `resource`.`id` AS resource_id, 
                `resource`.`name` AS resource_name, 
                `resource`.`description` AS resource_description, 
                `item_subtype`.`id` AS resource_item_subtype_id,
                `item_subtype`.`name` AS resource_item_subtype_name,
                `item_subtype`.`description` AS resource_item_subtype_description,
                COUNT({$this->sub_table}.item_id) AS count
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
            ->join('resource_item_subtype', 'resource_item_subtype.resource_id', 'resource.id')
            ->join('item_subtype', 'resource_item_subtype.item_subtype_id', 'item_subtype.id')
            ->where("resource_type.id", "=", $resource_type_id)
            ->where("resource.id", "=", $resource_id);

        if (array_key_exists('complete', $parameters) === true) {
            $collection->where($this->sub_table . '.complete', '=', 1);
        }

        $collection = Utility::applySearchClauses(
            $collection,
            $this->sub_table,
            $search_parameters
        );
        $collection = Utility::applyFilteringClauses(
            $collection,
            $this->sub_table,
            $filter_parameters
        );

        return $collection
            ->groupBy('resource.id', 'item_subtype.id')
            ->get()
            ->toArray();
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
        $collection = $this
            ->selectRaw("
                `resource`.`id` AS resource_id, 
                `resource`.`name` AS resource_name, 
                `resource`.`description` AS resource_description,
                `item_subtype`.`id` AS resource_item_subtype_id,
                `item_subtype`.`name` AS resource_item_subtype_name,
                `item_subtype`.`description` AS resource_item_subtype_description,
                COUNT({$this->sub_table}.item_id) AS count
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
            ->join('resource_item_subtype', 'resource_item_subtype.resource_id', 'resource.id')
            ->join('item_subtype', 'resource_item_subtype.item_subtype_id', 'item_subtype.id')
            ->where("resource_type.id", "=", $resource_type_id)
            ->where("resource.id", "=", $resource_id);

        if (array_key_exists('complete', $parameters) === true) {
            $collection->where($this->sub_table . '.complete', '=', 1);
        }

        return $collection
            ->groupBy('resource.id', 'item_subtype.id')
            ->get()
            ->toArray();
    }
}
