<?php
declare(strict_types=1);

namespace App\Models;

use App\Utilities\Model as ModelUtility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Error log
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class PermittedUser extends Model
{
    protected $table = 'permitted_user';

    protected $guarded = ['id'];

    /**
     * Return an instance of a permitted user
     *
     * @param integer $resource_type_id
     * @param integer $user_id
     *
     * @return PermittedUser|null
     */
    public function instance(int $resource_type_id, int $user_id): ?PermittedUser
    {
        return $this->where('resource_type_id', '=', $resource_type_id)->
            where('user_id', '=', $user_id)->
            first();
    }

    /**
     * Validate that the category exists and is accessible to the user for
     * viewing, editing based on their permitted resource types
     *
     * @param integer $resource_type_id
     * @param integer $category_id
     * @param array $permitted_resource_types
     * @param boolean $manage Should be exclude public items as we are checking
     * a management route
     *
     * @return boolean
     */
    public function categoryExistsToUser(
        int $resource_type_id,
        int $category_id,
        array $permitted_resource_types,
        $manage = false
    ): bool
    {
        $collection = $this->from('category')->
            where('category.id', '=', $category_id)->
            join('resource_type', 'category.resource_type_id', 'resource_type.id')->
            where('category.resource_type_id', '=', $resource_type_id);

        $collection = ModelUtility::applyResourceTypeCollectionCondition(
            $collection,
            $permitted_resource_types,
            ($manage === true) ? false : true
        );

        return (count($collection->get()) === 1) ? true : false;
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
    public function itemCategoryExistsToUser(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        int $item_category_id,
        array $permitted_resource_types,
        $manage = false
    ): bool
    {
        $collection = $this->from('item_category')->
            join('item', 'item_category.item_id', 'item.id')->
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

    /**
     * Validate that the item exists and is accessible to the user for
     * viewing, editing etc. based on their permitted resource types
     *
     * @param integer $resource_type_id
     * @param integer $resource_id
     * @param integer $item_id
     * @param array $permitted_resource_types
     * @param boolean $manage Should be exclude public items as we are checking
     * a management route
     *
     * @return boolean
     */
    public function itemExistsToUser(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        array $permitted_resource_types,
        $manage = false
    ): bool
    {
        $collection = $this->from('item')->
            join('resource', 'item.resource_id', 'resource.id')->
            join('resource_type', 'resource.resource_type_id', 'resource_type.id')->
            where('resource.resource_type_id', '=', $resource_type_id)->
            where('resource.id', '=', $resource_id)->
            where('item.id', '=', $item_id);

        $collection = ModelUtility::applyResourceTypeCollectionCondition(
            $collection,
            $permitted_resource_types,
            ($manage === true) ? false : true
        );

        return (count($collection->get()) === 1) ? true : false;
    }

    /**
     * Validate that the item exists and is accessible to the user for
     * viewing, editing etc. based on their permitted resource types
     *
     * @param integer $resource_type_id
     * @param integer $resource_id
     * @param integer $item_id
     * @param integer $item_category_id
     * @param integer $item_subcategory_id
     * @param array $permitted_resource_types
     * @param boolean $manage Should be exclude public items as we are checking
     * a management route
     *
     * @return boolean
     */
    public function itemSubcategoryExistsToUser(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        int $item_category_id,
        int $item_subcategory_id,
        array $permitted_resource_types,
        $manage = false
    ): bool
    {
        $collection = $this->from('item_sub_category')->
            join('item_category', 'item_sub_category.item_category_id', 'item_category.id')->
            join('item', 'item_category.item_id', 'item.id')->
            join('resource', 'item.resource_id', 'resource.id')->
            join('resource_type', 'resource.resource_type_id', 'resource_type.id')->
            where('resource.resource_type_id', '=', $resource_type_id)->
            where('resource.id', '=', $resource_id)->
            where('item.id', '=', $item_id)->
            where('item_category.id', '=', $item_category_id)->
            where('item_sub_category.id', '=', $item_subcategory_id);

        $collection = ModelUtility::applyResourceTypeCollectionCondition(
            $collection,
            $permitted_resource_types,
            ($manage === true) ? false : true
        );

        return (count($collection->get()) === 1) ? true : false;
    }

    /**
     * Validate that the item type exists
     *
     * @param integer $id
     *
     * @return boolean
     */
    public function itemTypeExistsToUser(int $id): bool
    {
        $collection = $this->from('item_type')->
            where('item_type.id', '=', $id);

        return (count($collection->get()) === 1) ? true : false;
    }

    /**
     * Fetch all the resource types the user has access to
     *
     * @param integer $user_id
     *
     * @return array
     */
    public function permittedResourceTypes(int $user_id): array
    {
        $permitted = [];

        $results = $this->where('user_id', '=', $user_id)->
            select('resource_type_id')->
            get()->
            toArray();

        foreach ($results as $row) {
            $permitted[] = $row['resource_type_id'];
        }

        return $permitted;
    }

    /**
     * Validate that the resource exists and is accessible to the user for
     * viewing, editing etc. based on their permitted resource types
     *
     * @param integer $resource_id
     * @param integer $resource_type_id
     * @param array $permitted_resource_types
     * @param boolean $manage Should be exclude public items as we are checking
     * a management route
     *
     * @return boolean
     */
    public function resourceExistsToUser(
        int $resource_id,
        int $resource_type_id,
        array $permitted_resource_types,
        $manage = false
    ): bool
    {
        $collection = $this->from('resource')->
            join('resource_type', 'resource.resource_type_id', 'resource_type.id')->
            where('resource.resource_type_id', '=', $resource_type_id)->
            where('resource.id', '=', $resource_id);

        $collection = ModelUtility::applyResourceTypeCollectionCondition(
            $collection,
            $permitted_resource_types,
            ($manage === true) ? false : true
        );

        return (count($collection->get()) === 1) ? true : false;
    }

    /**
     * Validate that the resource type exists and is accessible to the user for
     * viewing, editing
     *
     * @param integer $id
     * @param array $permitted_resource_types
     * @param boolean $manage Should be exclude public items as we are checking
     * a management route
     *
     * @return boolean
     */
    public function resourceTypeExistsToUser(
        int $id,
        array $permitted_resource_types,
        $manage = false
    ): bool
    {
        $collection = $this->from('resource_type')->
            where('resource_type.id', '=', $id);

        $collection = ModelUtility::applyResourceTypeCollectionCondition(
            $collection,
            $permitted_resource_types,
            ($manage === true) ? false : true
        );

        return (count($collection->get()) === 1) ? true : false;
    }

    /**
     * Validate that the subcategory exists and is accessible to the user for
     * viewing, editing based on their permitted resource types
     *
     * @param integer $resource_type_id
     * @param integer $category_id
     * @param integer $subcategory_id
     * @param array $permitted_resource_types
     * @param boolean $manage Should be exclude public items as we are checking
     * a management route
     *
     * @return boolean
     */
    public function subcategoryExistsToUser(
        int $resource_type_id,
        int $category_id,
        int $subcategory_id,
        array $permitted_resource_types,
        $manage = false
    ): bool
    {
        $collection = $this->from('sub_category')->
            where('sub_category.id', '=', $subcategory_id)->
            join('category', 'sub_category.category_id', 'category.id')->
            join('resource_type', 'category.resource_type_id', 'resource_type.id')->
            where('sub_category.category_id', '=', $category_id)->
            where('category.resource_type_id', '=', $resource_type_id);

        $collection = ModelUtility::applyResourceTypeCollectionCondition(
            $collection,
            $permitted_resource_types,
            ($manage === true) ? false : true
        );

        return (count($collection->get()) === 1) ? true : false;
    }
}
