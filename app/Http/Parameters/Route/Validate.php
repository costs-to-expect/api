<?php

namespace App\Http\Parameters\Route;

use App\Http\Parameters\Route\Validators\Category;
use App\Http\Parameters\Route\Validators\Item;
use App\Http\Parameters\Route\Validators\ItemCategory;
use App\Http\Parameters\Route\Validators\ItemSubCategory;
use App\Http\Parameters\Route\Validators\Resource;
use App\Http\Parameters\Route\Validators\ResourceType;
use App\Http\Parameters\Route\Validators\SubCategory;

/**
 * Validate the set route parameters, redirect to 404 if invalid
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Validate
{
    static public function category($category_id)
    {
        if (Category::validate($category_id) === false) {
            self::return404('Category not found');
        }
    }

    static public function subCategory($category_id, $sub_category_id)
    {
        if (SubCategory::validate($category_id, $sub_category_id) === false) {
            self::return404('Sub category not found');
        }
    }

    static public function resourceType($resource_type_id)
    {
        if (ResourceType::validate($resource_type_id) === false) {
            self::return404('Resource type not found');
        }
    }

    static public function resource($resource_type_id, $resource_id)
    {
        if (Resource::validate($resource_type_id, $resource_id) === false) {
            self::return404('Resource not found');
        }
    }

    static public function item($resource_type_id, $resource_id, $item_id)
    {
        if (Item::validate($resource_type_id, $resource_id, $item_id) === false) {
            self::return404('Item not found');
        }
    }

    static public function itemCategory(
        $resource_type_id,
        $resource_id,
        $item_id,
        $item_category_id
    ) {
        if (ItemCategory::validate(
                $resource_type_id,
                $resource_id,
                $item_id,
                $item_category_id
            ) === false
        ) {
            self::return404('Item category not found');
        }
    }

    static public function itemSubCategory(
        $resource_type_id,
        $resource_id,
        $item_id,
        $item_category_id,
        $item_sub_category_id
    ) {
        if (ItemSubCategory::validate(
                $resource_type_id,
                $resource_id,
                $item_id,
                $item_category_id,
                $item_sub_category_id
            ) === false
        ) {
            self::return404('Item sub category not found');
        }
    }

    static protected function return404($message)
    {
        response()->json(
            [
                'message' => $message
            ],
            404
        )->send();
        die;
    }
}
