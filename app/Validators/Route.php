<?php
declare(strict_types=1);

namespace App\Validators;

use App\Validators\Routes\Category;
use App\Validators\Routes\Item;
use App\Validators\Routes\ItemCategory;
use App\Validators\Routes\ItemSubcategory;
use App\Validators\Routes\ItemType;
use App\Validators\Routes\Resource;
use App\Validators\Routes\ResourceType;
use App\Validators\Routes\Subcategory;
use App\Utilities\Response as UtilityResponse;

/**
 * Validate the set route parameters, redirect to 404 if invalid
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Route
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
    static public function category(
        int $resource_type_id,
        int $category_id,
        array $permitted_resource_types,
        bool $write = false
    )
    {
        if ($write === false) {
            if (
                Category::existsToUserForViewing(
                    $resource_type_id,
                    $category_id,
                    $permitted_resource_types
                ) === false
            ) {
                UtilityResponse::notFound(trans('entities.category'));
            }
        } else {
            if (
                Category::existsToUserForManagement(
                    $resource_type_id,
                    $category_id,
                    $permitted_resource_types
                ) === false
            ) {
                UtilityResponse::notFoundOrNotAccessible(trans('entities.category'));
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
    static public function subcategory(
        int $resource_type_id,
        int $category_id,
        int $subcategory_id,
        array $permitted_resource_types,
        $write = false
    )
    {
        if ($write === false) {
            if (
                Subcategory::existsToUserForViewing(
                    $resource_type_id,
                    $category_id,
                    $subcategory_id,
                    $permitted_resource_types
                ) === false
            ) {
                UtilityResponse::notFound(trans('entities.subcategory'));
            }
        } else {
            if (
                Subcategory::existsToUserForManagement(
                    (int) $resource_type_id,
                    (int) $category_id,
                    (int) $subcategory_id,
                    $permitted_resource_types
                ) === false
            ) {
                UtilityResponse::notFoundOrNotAccessible(trans('entities.subcategory'));
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
    static public function resourceType(
        $resource_type_id,
        array $permitted_resource_types,
        bool $write = false
    )
    {
         if ($write === false) {
            if (
                ResourceType::existsToUserForViewing(
                    (int) $resource_type_id,
                    $permitted_resource_types
                ) === false
            ) {
                UtilityResponse::notFound(trans('entities.resource-type'));
            }
        } else {
            if (
                ResourceType::existsToUserForManagement(
                    (int) $resource_type_id,
                    $permitted_resource_types
                ) === false
            ) {
                UtilityResponse::notFoundOrNotAccessible(trans('entities.resource-type'));
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
    static public function resource(
        $resource_type_id,
        $resource_id,
        array $permitted_resource_types,
        bool $write = false
    )
    {
        if ($write === false) {
            if (
                Resource::existsToUserForViewing(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    $permitted_resource_types
                ) === false
            ) {
                UtilityResponse::notFound(trans('entities.resource'));
            }
        } else {
            if (
                Resource::existsToUserForManagement(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    $permitted_resource_types
                ) === false
            ) {
                UtilityResponse::notFoundOrNotAccessible(trans('entities.resource'));
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
    static public function item(
        $resource_type_id,
        $resource_id,
        $item_id,
        array $permitted_resource_types,
        bool $write = false
    )
    {
        if ($write === false) {
            if (
                Item::existsToUserForViewing(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    (int) $item_id,
                    $permitted_resource_types
                ) === false
            ) {
                UtilityResponse::notFound(trans('entities.item'));
            }
        } else {
            if (
                Item::existsToUserForManagement(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    (int) $item_id,
                    $permitted_resource_types
                ) === false
            ) {
                UtilityResponse::notFoundOrNotAccessible(trans('entities.item'));
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
    static public function itemCategory(
        $resource_type_id,
        $resource_id,
        $item_id,
        $item_category_id,
        array $permitted_resource_types,
        bool $write = false
    ) {
        if ($write === false) {
            if (
                ItemCategory::existsToUserForViewing(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    (int) $item_id,
                    (int) $item_category_id,
                    $permitted_resource_types
                ) === false
            ) {
                UtilityResponse::notFound(trans('entities.item-category'));
            }
        } else {
            if (
                ItemCategory::existsToUserForManagement(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    (int) $item_id,
                    (int) $item_category_id,
                    $permitted_resource_types
                ) === false
            ) {
                UtilityResponse::notFoundOrNotAccessible(trans('entities.item-category'));
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
    static public function itemType($item_type_id)
    {
        if (ItemType::existsToUserForViewing((int) $item_type_id) === false) {
            UtilityResponse::notFound(trans('entities.item-type'));
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
    static public function itemSubcategory(
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
                ItemSubcategory::existsToUserForViewing(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    (int) $item_id,
                    (int) $item_category_id,
                    (int) $item_subcategory_id,
                    $permitted_resource_types
                ) === false
            ) {
                UtilityResponse::notFound(trans('entities.item-subcategory'));
            }
        } else {
            if (
                ItemSubcategory::existsToUserForManagement(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    (int) $item_id,
                    (int) $item_category_id,
                    (int) $item_subcategory_id,
                    $permitted_resource_types
                ) === false
            ) {
                UtilityResponse::notFoundOrNotAccessible(trans('entities.item-subcategory'));
            }
        }
    }
}
