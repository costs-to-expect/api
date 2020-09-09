<?php
declare(strict_types=1);

namespace App\Request\Route;

/**
 * Validate the set route parameters, redirect to 404 if invalid
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Validate
{
    /**
     * Validate the route, checks the route parameters based on the users
     * permitted resource types
     *
     * @param integer $resource_type_id
     * @param integer $category_id
     * @param array $permitted_resource_types
     * @param bool $write
     */
    public static function category(
        int $resource_type_id,
        int $category_id,
        array $permitted_resource_types,
        bool $write = false
    )
    {
        if ($write === false) {
            if (
                Validator\Category::existsToUserForViewing(
                    $resource_type_id,
                    $category_id,
                    $permitted_resource_types
                ) === false
            ) {
                \App\Response\Responses::notFound(trans('entities.category'));
            }
        } else {
            if (
                Validator\Category::existsToUserForManagement(
                    $resource_type_id,
                    $category_id,
                    $permitted_resource_types
                ) === false
            ) {
                \App\Response\Responses::notFoundOrNotAccessible(trans('entities.category'));
            }
        }
    }


    /**
     * Validate the route, checks the route parameters based on the users
     * permitted resource types
     *
     * @param integer $resource_type_id
     * @param integer $category_id
     * @param integer $subcategory_id
     * @param array $permitted_resource_types
     * @param bool $write
     */
    public static function subcategory(
        int $resource_type_id,
        int $category_id,
        int $subcategory_id,
        array $permitted_resource_types,
        $write = false
    )
    {
        if ($write === false) {
            if (
                Validator\Subcategory::existsToUserForViewing(
                    $resource_type_id,
                    $category_id,
                    $subcategory_id,
                    $permitted_resource_types
                ) === false
            ) {
                \App\Response\Responses::notFound(trans('entities.subcategory'));
            }
        } else {
            if (
                Validator\Subcategory::existsToUserForManagement(
                    $resource_type_id,
                    $category_id,
                    $subcategory_id,
                    $permitted_resource_types
                ) === false
            ) {
                \App\Response\Responses::notFoundOrNotAccessible(trans('entities.subcategory'));
            }
        }
    }

    /**
     * Validate the route, checks the route parameters based on the users
     * permitted resource types
     *
     * @param $resource_type_id
     * @param array $permitted_resource_types
     * @param bool $write
     */
    public static function resourceType(
        $resource_type_id,
        array $permitted_resource_types,
        bool $write = false
    )
    {
         if ($write === false) {
            if (
                Validator\ResourceType::existsToUserForViewing(
                    (int) $resource_type_id,
                    $permitted_resource_types
                ) === false
            ) {
                \App\Response\Responses::notFound(trans('entities.resource-type'));
            }
        } else {
            if (
                Validator\ResourceType::existsToUserForManagement(
                    (int) $resource_type_id,
                    $permitted_resource_types
                ) === false
            ) {
                \App\Response\Responses::notFoundOrNotAccessible(trans('entities.resource-type'));
            }
        }
    }

    /**
     * Validate the route, checks the route parameters based on the users
     * permitted resource types
     *
     * @param $resource_type_id
     * @param $resource_id
     * @param array $permitted_resource_types
     * @param bool $write
     */
    public static function resource(
        $resource_type_id,
        $resource_id,
        array $permitted_resource_types,
        bool $write = false
    )
    {
        if ($write === false) {
            if (
                Validator\Resource::existsToUserForViewing(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    $permitted_resource_types
                ) === false
            ) {
                \App\Response\Responses::notFound(trans('entities.resource'));
            }
        } else {
            if (
                Validator\Resource::existsToUserForManagement(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    $permitted_resource_types
                ) === false
            ) {
                \App\Response\Responses::notFoundOrNotAccessible(trans('entities.resource'));
            }
        }
    }

    /**
     * Validate the route, checks the route parameters based on the users
     * permitted resource types
     *
     * @param $resource_type_id
     * @param $resource_id
     * @param $item_id
     * @param array $permitted_resource_types
     * @param bool $write
     */
    public static function item(
        $resource_type_id,
        $resource_id,
        $item_id,
        array $permitted_resource_types,
        bool $write = false
    )
    {
        if ($write === false) {
            if (
                Validator\Item::existsToUserForViewing(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    (int) $item_id,
                    $permitted_resource_types
                ) === false
            ) {
                \App\Response\Responses::notFound(trans('entities.item'));
            }
        } else {
            if (
                Validator\Item::existsToUserForManagement(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    (int) $item_id,
                    $permitted_resource_types
                ) === false
            ) {
                \App\Response\Responses::notFoundOrNotAccessible(trans('entities.item'));
            }
        }
    }

    /**
     * Validate the route, checks the route parameters based on the users
     * permitted resource types
     *
     * @param $resource_type_id
     * @param $resource_id
     * @param $item_id
     * @param $item_category_id,
     * @param array $permitted_resource_types
     * @param bool $write
     */
    public static function itemCategory(
        $resource_type_id,
        $resource_id,
        $item_id,
        $item_category_id,
        array $permitted_resource_types,
        bool $write = false
    ) {
        if ($write === false) {
            if (
                Validator\ItemCategory::existsToUserForViewing(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    (int) $item_id,
                    (int) $item_category_id,
                    $permitted_resource_types
                ) === false
            ) {
                \App\Response\Responses::notFound(trans('entities.item-category'));
            }
        } else {
            if (
                Validator\ItemCategory::existsToUserForManagement(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    (int) $item_id,
                    (int) $item_category_id,
                    $permitted_resource_types
                ) === false
            ) {
                \App\Response\Responses::notFoundOrNotAccessible(trans('entities.item-category'));
            }
        }
    }

    /**
     * Validate the route, checks the route parameters based on the users
     * permitted resource types
     *
     * (In this case that doesn't happen as anyone can see the item type)
     *
     * @param $item_type_id
     */
    public static function itemType($item_type_id)
    {
        if (Validator\ItemType::existsToUserForViewing((int) $item_type_id) === false) {
            \App\Response\Responses::notFound(trans('entities.item-type'));
        }
    }

    /**
     * Validate the route, checks the route parameters based on the users
     * permitted resource types
     *
     * @param $resource_type_id
     * @param $resource_id
     * @param $item_id
     * @param $item_category_id
     * @param $item_subcategory_id
     * @param array $permitted_resource_types
     * @param bool $write
     */
    public static function itemSubcategory(
        $resource_type_id,
        $resource_id,
        $item_id,
        $item_category_id,
        $item_subcategory_id,
        array $permitted_resource_types,
        bool $write = false
    ) {
        if ($write === false) {
            if (
                Validator\ItemSubcategory::existsToUserForViewing(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    (int) $item_id,
                    (int) $item_category_id,
                    (int) $item_subcategory_id,
                    $permitted_resource_types
                ) === false
            ) {
                \App\Response\Responses::notFound(trans('entities.item-subcategory'));
            }
        } else {
            if (
                Validator\ItemSubcategory::existsToUserForManagement(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    (int) $item_id,
                    (int) $item_category_id,
                    (int) $item_subcategory_id,
                    $permitted_resource_types
                ) === false
            ) {
                \App\Response\Responses::notFoundOrNotAccessible(trans('entities.item-subcategory'));
            }
        }
    }
}
