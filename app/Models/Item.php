<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin QueryBuilder
 *
 * @property int $id
 * @property int $resource_id
 * @property int $created_by
 * @property string $created_at
 * @property int $updated_by
 * @property string $updated_at
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Item extends Model
{
    protected $table = 'item';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class, 'resource_id', 'id');
    }

    public function instance(
        int $resource_type_id,
        int $resource_id,
        int $item_id
    ): ?Item {
        return $this->where('resource_id', '=', $resource_id)->
            join('resource', 'item.resource_id', 'resource.id')->
            where('resource.resource_type_id', '=', $resource_type_id)->
            select(
                'item.id'
            )
            ->find($item_id);
    }
}
