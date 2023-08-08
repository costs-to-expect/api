<?php

declare(strict_types=1);

namespace App\ItemType\BudgetPro\Models;

use App\Models\Utility;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Model as LaravelModel;
use Illuminate\Database\Query\Builder as QueryBuilder;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Item extends LaravelModel
{
    protected $table = 'item_type_budget_pro';

    protected $guarded = ['id'];

    public $timestamps = false;

    public function currency()
    {
        return $this->hasOne(Currency::class, 'id', 'currency_id');
    }

    public function instance(int $item_id): ?Item
    {
        return $this->where('item_id', '=', $item_id)->
            select(
                "{$this->table}.id"
            )->
            first();
    }

    #[ArrayShape([
        'item_id' => "int",
        'item_name' => "string",
        'item_account' => "string",
        'item_target_account' => "string",
        'item_description' => "string",
        'item_amount' => "float",
        'item_currency_id' => "int",
        'item_currency_code' => "string",
        'item_currency_name' => "string",
        'item_category' => "string",
        'item_start_date' => "string",
        'item_end_date' => "string",
        'item_disabled' => "int",
        'item_deleted' => "int",
        'item_frequency' => "string",
        'item_created_at' => "string",
        'item_updated_at' => "string"
    ])]
    public function instanceToArray(Item $item): array
    {
        return [
            'item_id' => $item->item_id,
            'item_name' => $item->name,
            'item_account' => $item->account,
            'item_target_account' => $item->target_account,
            'item_description' => $item->description,
            'item_amount' => $item->amount,
            'item_currency_id' => $item->currency->id,
            'item_currency_code' => $item->currency->code,
            'item_currency_name' => $item->currency->name,
            'item_category' => $item->category,
            'item_start_date' => $item->start_date,
            'item_end_date' => $item->end_date,
            'item_disabled' => $item->disabled,
            'item_deleted' => $item->deleted,
            'item_frequency' => $item->frequency,
            'item_created_at' => ($item->created_at !== null) ? $item->created_at->toDateTimeString() : null,
            'item_updated_at' => ($item->updated_at !== null) ? $item->updated_at->toDateTimeString() : null,
        ];
    }

    public function single(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        array $parameters = []
    ): ?array {
        $fields = [
            'item.id AS item_id',
            "{$this->table}.name AS item_name",
            "{$this->table}.account AS item_account",
            "{$this->table}.target_account AS item_target_account",
            "{$this->table}.description AS item_description",
            "{$this->table}.amount AS item_amount",
            "currency.id AS item_currency_id",
            "currency.code AS item_currency_code",
            "currency.name AS item_currency_name",
            "{$this->table}.category AS item_category",
            "{$this->table}.start_date AS item_start_date",
            "{$this->table}.end_date AS item_end_date",
            "{$this->table}.disabled AS item_disabled",
            "{$this->table}.deleted AS item_deleted",
            "{$this->table}.frequency AS item_frequency",
            "{$this->table}.created_at AS item_created_at",
            "{$this->table}.updated_at AS item_updated_at"
        ];

        $result = $this
            ->from('item')
            ->join($this->table, 'item.id', "{$this->table}.item_id")
            ->join('resource', 'item.resource_id', 'resource.id')
            ->join('currency', $this->table . '.currency_id', 'currency.id')
            ->where('item.resource_id', '=', $resource_id)
            ->where('resource.resource_type_id', '=', $resource_type_id)
            ->where("{$this->table}.item_id", '=', $item_id)
            ->where('item.id', '=', $item_id);

        return $result->select($fields)->first()?->toArray();
    }

    public function totalCount(
        int $resource_type_id,
        int $resource_id,
        array $parameters = [],
        array $search_parameters = [],
        array $filter_parameters = []
    ): int {
        $collection = $this->from('item')
            ->join($this->table, 'item.id', $this->table . '.item_id')
            ->join('resource', 'item.resource_id', 'resource.id')
            ->join('currency', $this->table . '.currency_id', 'currency.id')
            ->where('resource_id', '=', $resource_id)
            ->where('resource.resource_type_id', '=', $resource_type_id);

        if (array_key_exists('complete', $parameters) === true) {
            $collection->where($this->table . '.complete', '=', 1);
        }

        $collection = Utility::applySearchClauses(
            $collection,
            $this->table,
            $search_parameters
        );
        $collection = Utility::applyFilteringClauses(
            $collection,
            $this->table,
            $filter_parameters
        );

        return $collection->count();
    }

    public function paginatedCollection(
        int $resource_type_id,
        int $resource_id,
        int $offset = 0,
        int $limit = 10,
        array $parameters = [],
        array $search_parameters = [],
        array $filter_parameters = [],
        array $sort_parameters = []
    ): array {
        $select_fields = [
            'item.id AS item_id',
            "{$this->table}.name AS item_name",
            "{$this->table}.account AS item_account",
            "{$this->table}.target_account AS item_target_account",
            "{$this->table}.description AS item_description",
            "{$this->table}.amount AS item_amount",
            "currency.id AS item_currency_id",
            "currency.code AS item_currency_code",
            "currency.name AS item_currency_name",
            "{$this->table}.category AS item_category",
            "{$this->table}.start_date AS item_start_date",
            "{$this->table}.end_date AS item_end_date",
            "{$this->table}.disabled AS item_disabled",
            "{$this->table}.deleted AS item_deleted",
            "{$this->table}.frequency AS item_frequency",
            "{$this->table}.created_at AS item_created_at",
            "{$this->table}.updated_at AS item_updated_at"
        ];

        $collection = $this
            ->from('item')
            ->join($this->table, 'item.id', "{$this->table}.item_id")
            ->join('resource', 'item.resource_id', 'resource.id')
            ->join('currency', $this->table . '.currency_id', 'currency.id')
            ->where('resource_id', '=', $resource_id)
            ->where('resource.resource_type_id', '=', $resource_type_id);

        $collection = Utility::applySearchClauses(
            $collection,
            $this->table,
            $search_parameters
        );
        $collection = Utility::applyFilteringClauses(
            $collection,
            $this->table,
            $filter_parameters
        );

        if (count($sort_parameters) > 0) {
            foreach ($sort_parameters as $field => $direction) {
                switch ($field) {
                    case 'created':
                        $collection->orderBy($this->table . '.created_at', $direction);
                        break;

                    default:
                        $collection->orderBy($this->table . '.' . $field, $direction);
                        break;
                }
            }
        } else {
            $collection->orderBy('item.created_at', 'desc');
        }

        return $collection
            ->offset($offset)
            ->limit($limit)
            ->select($select_fields)
            ->selectRaw("(
                    SELECT 
                        GREATEST(
                            MAX(`{$this->table}`.`created_at`), 
                            IFNULL(MAX(`{$this->table}`.`updated_at`), 0),
                            0
                        )
                    FROM 
                        `{$this->table}` 
                    JOIN 
                        `item` ON 
                            `{$this->table}`.`item_id` = `item`.`id`
                    WHERE
                        `item`.`resource_id` = ? 
                ) AS `last_updated`",
                [
                    $resource_id
                ]
            )
            ->get()
            ->toArray();
    }

    public function hasCategoryAssignments(int $item_id): bool
    {
        return false;
    }
}
