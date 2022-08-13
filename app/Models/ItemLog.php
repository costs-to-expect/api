<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @property int $id
 * @property int $item_id
 * @property string $message
 * @property string $parameters
 * @property string $created_at
 * @property string $updated_at
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemLog extends Model
{
    protected $table = 'item_log';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    #[ArrayShape([
        'item_log_id' => "string",
        'item_log_message' => "string",
        'item_log_parameters' => "json",
        'item_log_created_at' => "string",
        'item_log_updated_at' => "string"
    ])]
    public function instanceToArray(Model $item_log): array
    {
        return [
            'item_log_id' => $item_log->id,
            'item_log_message' => $item_log->message,
            'item_log_parameters' => $item_log->parameters,
            'item_log_created_at' => $item_log->created_at->toDateTimeString(),
            'item_log_updated_at' => $item_log->updated_at->toDateTimeString()
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
            ->select("item_log.id")
            ->join('item', 'item_log.item_id', 'item.id')
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
                'item_log.id AS item_log_id',
                'item_log.message AS item_log_message',
                'item_log.parameters AS item_log_parameters',
                'item_log.created_at AS item_log_created_at',
                'item_log.updated_at AS item_log_updated_at',
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
            ->join('item', 'item_log.item_id', 'item.id')
            ->join('resource', 'item.resource_id', 'resource.id')
            ->join('resource_type', 'resource.resource_type_id', 'resource_type.id')
            ->where('item.id', '=', $item_id)
            ->where('resource.id', '=', $resource_id)
            ->where('resource_type.id', '=', $resource_type_id);

        $collection = Clause::applyViewableResourceTypes(
            $collection,
            $viewable_resource_types
        );

        return $collection->orderBy('item_log_id', 'DESC')->get()->toArray();
    }

    public function single(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        int $item_log_id,
        array $viewable_resource_types
    ): ?array
    {
        $result = self::query()
            ->select(
                'item_log.id AS item_log_id',
                'item_log.message AS item_log_message',
                'item_log.parameters AS item_log_parameters',
                'item_log.created_at AS item_log_created_at',
                'item_log.updated_at AS item_log_updated_at',
            )
            ->join('item', 'item_log.item_id', 'item.id')
            ->join('resource', 'item.resource_id', 'resource.id')
            ->join('resource_type', 'resource.resource_type_id', 'resource_type.id')
            ->where('item.id', '=', $item_id)
            ->where('resource.id', '=', $resource_id)
            ->where('resource_type.id', '=', $resource_type_id)
            ->where('item_log.id', '=', $item_log_id);

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

    public function instance(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        int $item_log_id,
        array $viewable_resource_types
    ): ?ItemLog
    {
        $result = self::query()
            ->select(
                'item_log.id',
                'item_log.message',
                'item_log.parameters'
            )
            ->join('item', 'item_log.item_id', 'item.id')
            ->join('resource', 'item.resource_id', 'resource.id')
            ->join('resource_type', 'resource.resource_type_id', 'resource_type.id')
            ->where('item.id', '=', $item_id)
            ->where('resource.id', '=', $resource_id)
            ->where('resource_type.id', '=', $resource_type_id)
            ->where('item_log.id', '=', $item_log_id);

        $result = Clause::applyViewableResourceTypes(
            $result,
            $viewable_resource_types
        );

        return $result->first();
    }

    public function deleteLogEntries(int $item_id): ?int
    {
        return self::query()->where($this->table . '.item_id', '=', $item_id)->delete();
    }
}
