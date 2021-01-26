<?php
declare(strict_types=1);

namespace App\ItemType\Game;

use Illuminate\Database\Eloquent\Model as LaravelModel;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2021
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SummaryResourceTypeModel extends LaravelModel
{
    protected $guarded = ['id'];
    protected $table = 'item';
    protected $sub_table = 'item_type_game';

    public function summary(
        int $resource_type_id,
        array $parameters
    ): array
    {
        $collection = $this
            ->selectRaw("
                `resource_type`.`id` AS resource_type_id, 
                `resource_type`.`name` AS resource_type_name, 
                `resource_type`.`description` AS resource_type_description, 
                COUNT({$this->sub_table}.item_id) AS count
            ")
            ->selectRaw("
                (
                    SELECT 
                        GREATEST(
                            MAX(`{$this->sub_table}`.`created_at`), 
                            IFNULL(MAX(`{$this->sub_table}`.`updated_at`), 0)
                        )
                    FROM 
                        `{$this->sub_table}` 
                    JOIN 
                        `item` ON 
                            `{$this->sub_table}`.`item_id` = `{$this->table}`.`id`
                    JOIN 
                        `resource` ON 
                            `{$this->table}`.`resource_id` = `resource`.`id`
                    WHERE
                        `resource`.`resource_type_id` = ? 
                ) AS `last_updated`",
                [
                    $resource_type_id
                ]
            )
            ->join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")
            ->join('resource', 'item.resource_id', 'resource.id')
            ->join('resource_type', 'resource.resource_type_id', 'resource_type.id')
            ->where('resource_type.id', '=', $resource_type_id);

        return $collection
            ->groupBy('resource_type.id')
            ->get()
            ->toArray();
    }

    public function resourcesSummary(
        int $resource_type_id,
        array $parameters
    ): array
    {
        $collection = $this
            ->selectRaw("
                `resource`.`id` AS resource_id, 
                `resource`.`name` AS resource_name, 
                `resource`.`description` AS resource_description, 
                `item_subtype`.`id` AS resource_item_subtype_id,
                `item_subtype`.`name` AS resource_item_subtype_name,
                `item_subtype`.`description` AS resource_item_subtype_description,
                COUNT(`{$this->sub_table}`.`item_id`) AS count"
            )
            ->selectRaw("
                (
                    SELECT 
                        GREATEST(
                            MAX(`{$this->sub_table}`.`created_at`), 
                            IFNULL(MAX(`{$this->sub_table}`.`updated_at`), 0)
                        )
                    FROM 
                        `{$this->sub_table}` 
                    JOIN 
                        `item` ON 
                            `{$this->sub_table}`.`item_id` = `{$this->table}`.`id`
                    JOIN 
                        `resource` ON 
                            `{$this->table}`.`resource_id` = `resource`.`id`
                    WHERE
                        `resource`.`resource_type_id` = ? 
                ) AS `last_updated`",
                [
                    $resource_type_id
                ]
            )
            ->join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")
            ->join('resource', 'item.resource_id', 'resource.id')
            ->join('resource_item_subtype', 'resource_item_subtype.resource_id', 'resource.id')
            ->join('item_subtype', 'resource_item_subtype.item_subtype_id', 'item_subtype.id')
            ->join('resource_type', 'resource.resource_type_id', 'resource_type.id')
            ->where('resource_type.id', '=', $resource_type_id);

        if (array_key_exists('complete', $parameters) === true) {
            $collection->where($this->sub_table . '.complete', '=', 1);
        }

        return $collection
            ->groupBy('resource.id', 'item_subtype.id')
            ->orderBy('resource.name')
            ->get()
            ->toArray();
    }

    public function filteredSummary(
        int $resource_type_id,
        array $parameters = [],
        array $search_parameters = []
    ): array
    {
        $collection = $this
            ->selectRaw("
                COUNT({$this->sub_table}.item_id) AS count
            ")
            ->selectRaw("
                (
                    SELECT 
                        GREATEST(
                            MAX(`{$this->sub_table}`.`created_at`), 
                            IFNULL(MAX(`{$this->sub_table}`.`updated_at`), 0)
                        )
                    FROM 
                        `{$this->sub_table}` 
                    JOIN 
                        `item` ON 
                            `{$this->sub_table}`.`item_id` = `{$this->table}`.`id`
                    JOIN 
                        `resource` ON 
                            `{$this->table}`.`resource_id` = `resource`.`id`
                    WHERE
                        `resource`.`resource_type_id` = ? 
                ) AS `last_updated`",
                [
                    $resource_type_id
                ]
            )
            ->join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")
            ->join("resource", "resource.id", "item.resource_id")
            ->join("resource_type", "resource_type.id", "resource.resource_type_id")
            ->where("resource_type.id", "=", $resource_type_id);

        if (array_key_exists('complete', $parameters) === true) {
            $collection->where($this->sub_table . '.complete', '=', 1);
        }

        if (count($search_parameters) > 0) {
            foreach ($search_parameters as $field => $search_term) {
                $collection->where("{$this->sub_table}." . $field, 'LIKE', '%' . $search_term . '%');
            }
        }

        return $collection
            ->get()
            ->toArray();
    }
}
