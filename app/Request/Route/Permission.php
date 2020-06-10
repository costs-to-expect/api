<?php
declare(strict_types=1);

namespace App\Request\Route;

/**
 * Work out the permissions for each route, permissions are read and manage
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Permission
{
    /**
     * Returns the `read` and `manage` permission for the current user, checks
     * against their permitted resource types
     *
     * @param integer $resource_type_id
     * @param integer $category_id
     * @param array $permitted_resource_types
     *
     * @return array Two indexes, view and manage, values for both boolean
     */
    public static function category(
        $resource_type_id,
        $category_id,
        array $permitted_resource_types
    ): array
    {
        return [
            'view' => Validator\Category::existsToUserForViewing(
                (int) $resource_type_id,
                (int) $category_id,
                $permitted_resource_types
            ),
            'manage' => Validator\Category::existsToUserForManagement(
                (int) $resource_type_id,
                (int) $category_id,
                $permitted_resource_types
            )
        ];
    }


    /**
     * Returns the `view` and `manage` permission for the current user, checks
     * against their permitted resource types
     *
     * @param $resource_type_id
     * @param $category_id
     * @param $subcategory_id
     * @param array $permitted_resource_types
     *
     * @return array Two indexes, view and manage, values for both boolean
     */
    public static function subcategory(
        $resource_type_id,
        $category_id,
        $subcategory_id,
        array $permitted_resource_types
    ): array
    {
        return [
            'view' => Validator\Subcategory::existsToUserForViewing(
                (int) $resource_type_id,
                (int) $category_id,
                (int) $subcategory_id,
                $permitted_resource_types
            ),
            'manage' => Validator\Subcategory::existsToUserForManagement(
                (int) $resource_type_id,
                (int) $category_id,
                (int) $subcategory_id,
                $permitted_resource_types
            )
        ];
    }

    /**
     * Returns the `view` and `manage` permission for the current user, checks
     * against their permitted resource types
     *
     * @param $resource_type_id
     * @param array $permitted_resource_types
     *
     * @return array Two indexes, view and manage, values for both boolean
     */
    public static function resourceType(
        $resource_type_id,
        array $permitted_resource_types
    ): array
    {
        return [
            'view' => Validator\ResourceType::existsToUserForViewing(
                (int) $resource_type_id,
                $permitted_resource_types
            ),
            'manage' => Validator\ResourceType::existsToUserForManagement(
                (int) $resource_type_id,
                $permitted_resource_types
            )
        ];
    }

    /**
     * Returns the `view` and `manage` permission for the current user, checks
     * against their permitted resource types
     *
     * @param $resource_type_id
     * @param $resource_id
     * @param array $permitted_resource_types
     *
     * @return array Two indexes, view and manage, values for both boolean
     */
    public static function resource(
        $resource_type_id,
        $resource_id,
        array $permitted_resource_types
    ): array
    {
        return [
            'view' => Validator\Resource::existsToUserForViewing(
                (int) $resource_type_id,
                (int) $resource_id,
                $permitted_resource_types
            ),
            'manage' => Validator\Resource::existsToUserForManagement(
                (int) $resource_type_id,
                (int) $resource_id,
                $permitted_resource_types
            )
        ];
    }

    /**
     * Returns the `view` and `manage` permission for the current user, checks
     * against their permitted resource types
     *
     * @param $resource_type_id
     * @param $resource_id
     * @param $item_id
     * @param array $permitted_resource_types
     *
     * @return array Two indexes, view and manage, values for both boolean
     */
    public static function item(
        $resource_type_id,
        $resource_id,
        $item_id,
        array $permitted_resource_types
    ): array
    {
        return [
            'view' => Validator\Item::existsToUserForViewing(
                (int) $resource_type_id,
                (int) $resource_id,
                (int) $item_id,
                $permitted_resource_types
            ),
            'manage' => Validator\Item::existsToUserForManagement(
                (int) $resource_type_id,
                (int) $resource_id,
                (int) $item_id,
                $permitted_resource_types
            )
        ];
    }

    /**
     * Returns the `view` and `manage` permission for the current user, checks
     * against their permitted resource types
     *
     * @param $resource_type_id
     * @param $resource_id
     * @param $item_id
     * @param $item_category_id,
     * @param array $permitted_resource_types
     *
     * @return array Two indexes, view and manage, values for both boolean
     */
    public static function itemCategory(
        $resource_type_id,
        $resource_id,
        $item_id,
        $item_category_id,
        array $permitted_resource_types
    ): array
    {
        return [
            'view' => Validator\ItemCategory::existsToUserForViewing(
                (int) $resource_type_id,
                (int) $resource_id,
                (int) $item_id,
                (int) $item_category_id,
                $permitted_resource_types
            ),
            'manage' => Validator\ItemCategory::existsToUserForManagement(
                (int) $resource_type_id,
                (int) $resource_id,
                (int) $item_id,
                (int) $item_category_id,
                $permitted_resource_types
            )
        ];
    }

    /**
     * Returns the `view` and `manage` permission for the current user, checks
     * against their permitted resource types
     *
     * @param $resource_type_id
     * @param $resource_id
     * @param $item_id
     * @param $item_category_id
     * @param $item_subcategory_id
     * @param array $permitted_resource_types
     *
     * @return array Two indexes, view and manage, values for both boolean
     */
    public static function itemSubcategory(
        $resource_type_id,
        $resource_id,
        $item_id,
        $item_category_id,
        $item_subcategory_id,
        array $permitted_resource_types
    ): array
    {
        return [
        'view' => Validator\ItemSubcategory::existsToUserForViewing(
                (int) $resource_type_id,
                (int) $resource_id,
                (int) $item_id,
                (int) $item_category_id,
                (int) $item_subcategory_id,
                $permitted_resource_types
            ),
        'manage' => Validator\ItemSubcategory::existsToUserForManagement(
                (int) $resource_type_id,
                (int) $resource_id,
                (int) $item_id,
                (int) $item_category_id,
                (int) $item_subcategory_id,
                $permitted_resource_types
            )
        ];
    }
}
