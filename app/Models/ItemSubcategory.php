<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * Item model
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemSubcategory extends Model
{
    protected $table = 'item_sub_category';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function sub_category()
    {
        return $this->hasOne(Subcategory::class, 'id', 'sub_category_id');
    }

    public function item()
    {
        return $this->hasOne(Item::class, 'id', 'item_id');
    }

    public function item_category()
    {
        return $this->belongsTo(ItemCategory::class, 'item_category_id', 'id');
    }

    public function paginatedCollection(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        int $item_category_id,
        int $offset = 0,
        int $limit = 10
    )
    {
        return $this->join('sub_category', 'item_sub_category.sub_category_id', 'sub_category.id')->
            join('item_category', 'item_sub_category.item_category_id', 'item_category.id')->
            join('item', 'item_category.item_id', 'item.id')->
            join('resource', 'item.resource_id', 'resource.id')->
            where('item_category.id', '=', $item_category_id)->
            where('item_category.item_id', '=', $item_id)->
            where('resource.id', '=', $resource_id)->
            where('resource.resource_type_id', '=', $resource_type_id)->
            select(
                'item_sub_category.id AS item_sub_category_id',
                'item_sub_category.created_at AS item_sub_category_created_at',
                'sub_category.id AS item_sub_category_sub_category_id',
                'sub_category.name AS item_sub_category_sub_category_name',
                'sub_category.description AS item_sub_category_sub_category_description'
            )->
            get()->
            toArray();
    }

    public function single(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        int $item_category_id,
        int $item_subcategory_id
    ): ?array
    {
        $result = $this->join('sub_category', 'item_sub_category.sub_category_id', 'sub_category.id')->
            join('item_category', 'item_sub_category.item_category_id', 'item_category.id')->
            join('item', 'item_category.item_id', 'item.id')->
            join('resource', 'item.resource_id', 'resource.id')->
            where('item_category.id', '=', $item_category_id)->
            where('item_category.item_id', '=', $item_id)->
            where('resource.id', '=', $resource_id)->
            where('resource.resource_type_id', '=', $resource_type_id)->
            select(
                'item_sub_category.id AS item_sub_category_id',
                'item_sub_category.created_at AS item_sub_category_created_at',
                'sub_category.id AS item_sub_category_sub_category_id',
                'sub_category.name AS item_sub_category_sub_category_name',
                'sub_category.description AS item_sub_category_sub_category_description'
            )->
            find($item_subcategory_id);

        if ($result !== null) {
            return $result->toArray();
        } else {
            return null;
        }
    }

    public function instance(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        int $item_category_id,
        int $item_subcategory_id
    ): ?ItemSubcategory
    {
        return $this->join('sub_category', 'item_sub_category.sub_category_id', 'sub_category.id')->
            join('item_category', 'item_sub_category.item_category_id', 'item_category.id')->
            join('item', 'item_category.item_id', 'item.id')->
            join('resource', 'item.resource_id', 'resource.id')->
            where('item_category.id', '=', $item_category_id)->
            where('item_category.item_id', '=', $item_id)->
            where('resource.id', '=', $resource_id)->
            where('resource.resource_type_id', '=', $resource_type_id)->
            select(
            'item_sub_category.id AS id',
                'item_sub_category.id AS item_sub_category_id',
                'item_sub_category.created_at AS item_sub_category_created_at',
                'sub_category.id AS item_sub_category_sub_category_id',
                'sub_category.name AS item_sub_category_sub_category_name',
                'sub_category.description AS item_sub_category_sub_category_description'
            )->
            find($item_subcategory_id);
    }

    /**
     * Convert the model instance to an array for use with the transformer
     *
     * @param ItemSubcategory $item_sub_category
     *
     * @return array
     */
    public function instanceToArray(ItemSubcategory $item_sub_category): array
    {
        return [
            'item_sub_category_id' => $item_sub_category->id,
            'item_sub_category_created_at' => $item_sub_category->created_at->toDateTimeString(),
            'item_sub_category_sub_category_id' => $item_sub_category->sub_category->id,
            'item_sub_category_sub_category_name' => $item_sub_category->sub_category->name,
            'item_sub_category_sub_category_description' => $item_sub_category->sub_category->description
        ];
    }
}
