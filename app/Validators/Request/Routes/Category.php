<?php
declare(strict_types=1);

namespace App\Validators\Request\Routes;

use App\Models\Category as CategoryModel;

/**
 * Validate the route params to a category
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Category
{
    /**
     * Validate the route params are valid
     *
     * @param string|int $category_id
     *
     * @return boolean
     */
    static public function validate($category_id): bool
    {
        if ($category_id === 'nill' || (new CategoryModel)->find($category_id)->exists() === false) {
            return false;
        }

        return true;
    }
}
