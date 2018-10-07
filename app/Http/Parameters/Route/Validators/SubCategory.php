<?php

namespace App\Http\Parameters\Route\Validators;

use App\Models\Category as CategoryModel;
use App\Models\SubCategory as SubCategoryModel;

/**
 * Validate the route params to a sub category
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SubCategory
{
    /**
     * Validate the route params are valid
     *
     * @param string|int $category_id
     * @param string|int $sub_category_id
     *
     * @return boolean
     */
    static public function validate($category_id, $sub_category_id)
    {
        if (
            $category_id === 'nill' ||
            $sub_category_id === 'nill' ||
            (new CategoryModel)->find($category_id)->exists() === false ||
            (new SubCategoryModel())->
                find($sub_category_id)->
                where('category_id', '=', $category_id)->
                exists() === false
        ) {
            return false;
        }

        return true;
    }
}
