<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $item_id
 * @property string $key
 * @property string $value
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemData extends Model
{
    protected $table = 'item_data';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function totalCount(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        array $viewable_resource_types
    ): int
    {
        $collection = self::query()
            ->select("item_data.id")
            ->join('item', 'item_data.item_id', 'item.id')
            ->join('resource', 'item.resource_id', 'resource.id')
            ->join('resource_type', 'resource.resource_type_id', 'resource_type.id')
            ->where('item.id', '=', $item_id)
            ->where('resource.id', '=', $resource_id)
            ->where('resource_type.id', '=', $resource_type_id);

        $collection = Clause::applyViewableResourceTypes(
            $collection,
            $viewable_resource_types
        );

        return $collection->count();
    }

    public function collection(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        array $viewable_resource_types
    ): array
    {
        $collection = self::query()
            ->select(
                'item_data.key AS item_data_key',
                'item_data.value AS item_data_json',
                'item_data.created_at AS item_data_created_at',
                'item_data.updated_at AS item_data_updated_at',
            )
            ->selectRaw("(
                SELECT 
                    GREATEST(
                        MAX(`{$this->table}`.`created_at`), 
                        IFNULL(MAX(`{$this->table}`.`updated_at`), 0),
                        0
                    )
                FROM 
                    `{$this->table}`
                WHERE
                    `{$this->table}`.`item_id` = ? 
                ) AS `last_updated`",
                [
                    $item_id
                ]
            )
            ->join('item', 'item_data.item_id', 'item.id')
            ->join('resource', 'item.resource_id', 'resource.id')
            ->join('resource_type', 'resource.resource_type_id', 'resource_type.id')
            ->where('item.id', '=', $item_id)
            ->where('resource.id', '=', $resource_id)
            ->where('resource_type.id', '=', $resource_type_id);

        $collection = Clause::applyViewableResourceTypes(
            $collection,
            $viewable_resource_types
        );

        return $collection->get()->toArray();
    }

    public function single(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        string $key,
        array $viewable_resource_types
    ): ?array
    {
        $result = self::query()
            ->select(
                'item_data.key AS item_data_key',
                'item_data.value AS item_data_json',
                'item_data.created_at AS item_data_created_at',
                'item_data.updated_at AS item_data_updated_at',
            )
            ->join('item', 'item_data.item_id', 'item.id')
            ->join('resource', 'item.resource_id', 'resource.id')
            ->join('resource_type', 'resource.resource_type_id', 'resource_type.id')
            ->where('item.id', '=', $item_id)
            ->where('resource.id', '=', $resource_id)
            ->where('resource_type.id', '=', $resource_type_id)
            ->where('item_data.key', '=', $key);

        $result = Clause::applyViewableResourceTypes(
            $result,
            $viewable_resource_types
        );

        $result = $result->get()->toArray();

        if (count($result) === 0) {
            return null;
        }

        return $result[0];
    }
}
