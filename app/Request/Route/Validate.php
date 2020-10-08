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
     * @param integer|string $resource_type_id
     * @param integer|string $category_id
     * @param array $permitted_resource_types
     * @param bool $write
     */
    public static function category(
        $resource_type_id,
        $category_id,
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
     * @param integer|string $resource_type_id
     * @param integer|string $category_id
     * @param integer|string $subcategory_id
     * @param array $permitted_resource_types
     * @param bool $write
     */
    public static function subcategory(
        $resource_type_id,
        $category_id,
        $subcategory_id,
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
                    $resource_type_id,
                    $permitted_resource_types
                ) === false
            ) {
                \App\Response\Responses::notFound(trans('entities.resource-type'));
            }
        } else {
            if (
                Validator\ResourceType::existsToUserForManagement(
                    $resource_type_id,
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
                    $resource_type_id,
                    $resource_id,
                    $permitted_resource_types
                ) === false
            ) {
                \App\Response\Responses::notFound(trans('entities.resource'));
            }
        } else {
            if (
                Validator\Resource::existsToUserForManagement(
                    $resource_type_id,
                    $resource_id,
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
                    $resource_type_id,
                    $resource_id,
                    $item_id,
                    $permitted_resource_types
                ) === false
            ) {
                \App\Response\Responses::notFound(trans('entities.item'));
            }
        } else {
            if (
                Validator\Item::existsToUserForManagement(
                    $resource_type_id,
                    $resource_id,
                    $item_id,
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
                    $resource_type_id,
                    $resource_id,
                    $item_id,
                    $item_category_id,
                    $permitted_resource_types
                ) === false
            ) {
                \App\Response\Responses::notFound(trans('entities.item-category'));
            }
        } else {
            if (
                Validator\ItemCategory::existsToUserForManagement(
                    $resource_type_id,
                    $resource_id,
                    $item_id,
                    $item_category_id,
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
        if (Validator\ItemType::existsToUserForViewing($item_type_id) === false) {
            \App\Response\Responses::notFound(trans('entities.item-type'));
        }
    }

    /**
     * Validate the route, checks the route parameters based on the users
     * permitted resource types
     *
     * (In this case that doesn't happen as anyone can see the item type)
     *
     * @param $item_type_id
     * @param $item_subtype_id
     */
    public static function itemSubType($item_type_id, $item_subtype_id)
    {
        if (Validator\ItemSubtype::existsToUserForViewing($item_type_id, $item_subtype_id) === false) {
            \App\Response\Responses::notFound(trans('entities.item-type'));
        }
    }

    /**
     * Validate the route, checks the route parameters based on the users
     * permitted resource types
     *
     * (In this case that doesn't happen as anyone can see the currency)
     *
     * @param $currency_id
     */
    public static function currency($currency_id)
    {
        if (Validator\Currency::existsToUserForViewing($currency_id) === false) {
            \App\Response\Responses::notFound(trans('entities.currency'));
        }
    }

    /**
     * Validate the route, checks the route parameters based on the users
     * permitted resource types
     *
     * (In this case that doesn't happen as anyone can see the queue)
     *
     * @param $queue_id
     */
    public static function queue($queue_id)
    {
        if (Validator\Queue::existsToUserForViewing($queue_id) === false) {
            \App\Response\Responses::notFound(trans('entities.queue'));
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
                    $resource_type_id,
                    $resource_id,
                    $item_id,
                    $item_category_id,
                    $item_subcategory_id,
                    $permitted_resource_types
                ) === false
            ) {
                \App\Response\Responses::notFound(trans('entities.item-subcategory'));
            }
        } else {
            if (
                Validator\ItemSubcategory::existsToUserForManagement(
                    $resource_type_id,
                    $resource_id,
                    $item_id,
                    $item_category_id,
                    $item_subcategory_id,
                    $permitted_resource_types
                ) === false
            ) {
                \App\Response\Responses::notFoundOrNotAccessible(trans('entities.item-subcategory'));
            }
        }
    }
}
