<?php
declare(strict_types=1);

namespace App\Models;

use App\Utilities\Model as ModelUtility;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * Item category model
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemCategory extends Model
{
    protected $table = 'item_category';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function item()
    {
        return $this->hasOne(Item::class, 'id', 'item_id');
    }

    public function paginatedCollection(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        int $offset = 0,
        int $limit = 10
    )
    {
        return $this->join('category', 'item_category.category_id', 'category.id')->
            join('item', 'item_category.item_id', 'item.id')->
            join('resource', 'item.resource_id', 'resource.id')->
            where('item_category.item_id', '=', $item_id)->
            where('resource.id', '=', $resource_id)->
            where('resource.resource_type_id', '=', $resource_type_id)->
            select(
                'item_category.id AS item_category_id',
                'item_category.created_at AS item_category_created_at',
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
    ): ?array
    {
        $result = $this->join('category', 'item_category.category_id', 'category.id')->
            join('item', 'item_category.item_id', 'item.id')->
            join('resource', 'item.resource_id', 'resource.id')->
            where('item_category.item_id', '=', $item_id)->
            where('resource.id', '=', $resource_id)->
            where('resource.resource_type_id', '=', $resource_type_id)->
            select(
                'item_category.id AS item_category_id',
                'item_category.created_at AS item_category_created_at',
                'category.name AS item_category_category_name',
                'category.description AS item_category_category_description'
            )->
            find($item_category_id);

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
        int $item_category_id
    ): ?ItemCategory
    {
        return $this->join('category', 'item_category.category_id', 'category.id')->
            join('item', 'item_category.item_id', 'item.id')->
            join('resource', 'item.resource_id', 'resource.id')->
            where('item_category.item_id', '=', $item_id)->
            where('resource.id', '=', $resource_id)->
            where('resource.resource_type_id', '=', $resource_type_id)->
            select(
                'item_category.id AS item_category_id',
                'item_category.created_at AS item_category_created_at',
                'category.name AS item_category_category_name',
                'category.description AS item_category_category_description'
            )->
            find($item_category_id);
    }

    /**
     * Convert the model instance to an array for use with the transformer
     *
     * @param ItemCategory $item_category
     *
     * @return array
     */
    public function instanceToArray(ItemCategory $item_category): array
    {
        return [
            'item_category_id' => $item_category->id,
            'item_category_created_at' => $item_category->created_at->toDateTimeString(),
            'item_category_category_name' => $item_category->category->name,
            'item_category_category_description' => $item_category->category->description
        ];
    }

    /**
     * Validate that the item exists and is accessible to the user for
     * viewing, editing etc. based on their permitted resource types
     *
     * @param integer $resource_type_id
     * @param integer $resource_id
     * @param integer $item_id
     * @param integer $item_category_id
     * @param array $permitted_resource_types
     * @param boolean $manage Should be exclude public items as we are checking
     * a management route
     *
     * @return boolean
     */
    public function existsToUser(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        int $item_category_id,
        array $permitted_resource_types,
        $manage = false
    ): bool
    {
        $collection = $this->join('item', 'item_category.item_id', 'item.id')->
            join('resource', 'item.resource_id', 'resource.id')->
            join('resource_type', 'resource.resource_type_id', 'resource_type.id')->
            where('resource.resource_type_id', '=', $resource_type_id)->
            where('resource.id', '=', $resource_id)->
            where('item.id', '=', $item_id)->
            where('item_category.id', '=', $item_category_id);

        $collection = ModelUtility::applyResourceTypeCollectionCondition(
            $collection,
            $permitted_resource_types,
            ($manage === true) ? false : true
        );

        return (count($collection->get()) === 1) ? true : false;
    }
}
