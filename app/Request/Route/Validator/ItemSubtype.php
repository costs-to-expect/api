<?php
declare(strict_types=1);

namespace App\Request\Route\Validator;

use App\Models\ResourceTypeAccess;

/**
 * Validate the route params to an item subtype
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemSubtype
{
    /**
     * @param string|int $item_type_id
     * @param string|int $item_subtype_id
     *
     * @return boolean
     */
    public static function existsToUserForViewing($item_type_id, $item_subtype_id): bool
    {
        return !(
            $item_type_id === 'nill' ||
            $item_subtype_id === 'nill' ||
            (new ResourceTypeAccess())->itemSubTypeExistsToUser((int) $item_type_id, (int) $item_subtype_id) === false
        );
    }
}
