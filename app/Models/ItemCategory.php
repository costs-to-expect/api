<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @mixin QueryBuilder
 *
 * @property int $id
 * @property int $item_id
 * @property int $category_id
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemCategory extends Model
{
    protected $table = 'item_category';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function category(): HasOne
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function item(): HasOne
    {
        return $this->hasOne(Item::class, 'id', 'item_id');
    }

    public function numberAssigned(
        int $resource_type_id,
        int $resource_id,
        int $item_id
    ): int {
        return $this
            ->join('item', 'item_category.item_id', 'item.id')
            ->join('resource', 'item.resource_id', 'resource.id')
            ->where('item_category.item_id', '=', $item_id)
            ->where('resource.id', '=', $resource_id)
            ->where('resource.resource_type_id', '=', $resource_type_id)
            ->count('item_category.id');
    }

    public function paginatedCollection(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        int $offset = 0,
        int $limit = 10
    ): array {
        return $this->join('category', 'item_category.category_id', 'category.id')->
            join('item', 'item_category.item_id', 'item.id')->
            join('resource', 'item.resource_id', 'resource.id')->
            where('item_category.item_id', '=', $item_id)->
            where('resource.id', '=', $resource_id)->
            where('resource.resource_type_id', '=', $resource_type_id)->
            select(
                'item_category.id AS item_category_id',
                'item_category.created_at AS item_category_created_at',
                'category.id AS item_category_category_id',
                'category.name AS item_category_category_name',
                'category.description AS item_category_category_description'
            )->
            get()->
            toArray();
    }

    public function single(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        int $item_category_id
    ): ?array {
        $result = $this
            ->join('category', 'item_category.category_id', 'category.id')
            ->join('item', 'item_category.item_id', 'item.id')
            ->join('resource', 'item.resource_id', 'resource.id')
            ->where('item_category.item_id', '=', $item_id)
            ->where('resource.id', '=', $resource_id)
            ->where('resource.resource_type_id', '=', $resource_type_id)
            ->select(
                'item_category.id AS item_category_id',
                'item_category.created_at AS item_category_created_at',
                'category.id AS item_category_category_id',
                'category.name AS item_category_category_name',
                'category.description AS item_category_category_description'
            );

        $result = $result
            ->where($this->table . '.id', '=', $item_category_id)
            ->get()
            ->toArray();

        if (count($result) === 0) {
            return null;
        }

        return $result[0];
    }

    public function instance(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        int $item_category_id
    ): ?ItemCategory {
        return $this->join('category', 'item_category.category_id', 'category.id')->
            join('item', 'item_category.item_id', 'item.id')->
            join('resource', 'item.resource_id', 'resource.id')->
            where('item_category.item_id', '=', $item_id)->
            where('resource.id', '=', $resource_id)->
            where('resource.resource_type_id', '=', $resource_type_id)->
            select(
                'item_category.id AS id',
                'item_category.id AS item_category_id',
                'item_category.created_at AS item_category_created_at',
                'category.id AS item_category_category_id',
                'category.name AS item_category_category_name',
                'category.description AS item_category_category_description'
            )->
            find($item_category_id);
    }

    public function instanceToArray(ItemCategory $item_category): array
    {
        return [
            'item_category_id' => $item_category->id,
            'item_category_created_at' => $item_category->created_at->toDateTimeString(),
            'item_category_category_id' => $item_category->category->id,
            'item_category_category_name' => $item_category->category->name,
            'item_category_category_description' => $item_category->category->description
        ];
    }

    public function collectionByItemIds(
        int $resource_type_id,
        int $resource_id,
        array $item_ids
    ): array
    {
        return $this->query()
            ->join('category', 'item_category.category_id', 'category.id')
            ->join('item', 'item_category.item_id', 'item.id')
            ->join('resource', 'item.resource_id', 'resource.id')
            ->whereIn('item_category.item_id', $item_ids)
            ->where('resource.id', '=', $resource_id)
            ->where('resource.resource_type_id', '=', $resource_type_id)
            ->select(
                'item_category.item_id AS item_category_item_id',
                'item_category.id AS item_category_id',
                'item_category.created_at AS item_category_created_at',
                'category.id AS item_category_category_id',
                'category.name AS item_category_category_name',
                'category.description AS item_category_category_description'
            )
            ->get()
            ->toArray();
    }
}
