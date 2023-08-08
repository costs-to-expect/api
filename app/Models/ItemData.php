<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @property int $id
 * @property int $item_id
 * @property string $key
 * @property string $value
 * @property string $created_at
 * @property string $updated_at
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

    #[ArrayShape([
        'item_data_key' => "string",
        'item_data_json' => "json",
        'item_data_created_at' => "string",
        'item_data_updated_at' => "string"
    ])]
    public function instanceToArray(Model $item_data): array
    {
        return [
            'item_data_key' => $item_data->key,
            'item_data_json' => $item_data->value,
            'item_data_created_at' => $item_data->created_at->toDateTimeString(),
            'item_data_updated_at' => $item_data->updated_at->toDateTimeString()
        ];
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

        $collection = Utility::applyViewableResourceTypesClause(
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

        $collection = Utility::applyViewableResourceTypesClause(
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

        $result = Utility::applyViewableResourceTypesClause(
            $result,
            $viewable_resource_types
        );

        $result = $result->get()->toArray();

        if (count($result) === 0) {
            return null;
        }

        return $result[0];
    }

    public function instance(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        string $key,
        array $viewable_resource_types
    ): ?ItemData
    {
        $result = self::query()
            ->select(
                'item_data.id',
                'item_data.key',
                'item_data.value'
            )
            ->join('item', 'item_data.item_id', 'item.id')
            ->join('resource', 'item.resource_id', 'resource.id')
            ->join('resource_type', 'resource.resource_type_id', 'resource_type.id')
            ->where('item.id', '=', $item_id)
            ->where('resource.id', '=', $resource_id)
            ->where('resource_type.id', '=', $resource_type_id)
            ->where('item_data.key', '=', $key);

        $result = Utility::applyViewableResourceTypesClause(
            $result,
            $viewable_resource_types
        );

        return $result->first();
    }
}
