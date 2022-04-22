<?php
declare(strict_types=1);

namespace App\HttpRequest\Route\Validate;

use App\Models\ResourceAccess;

/**
 * Validate the route params to an item type
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemType
{
    /**
     * Validate that the user is able to view the requested item type
     *
     * @param string|int $item_type_id
     *
     * @return boolean
     */
    public static function existsToUserForViewing($item_type_id): bool
    {
        return !(
            (new ResourceAccess())->itemTypeExistsToUser((int) $item_type_id) === false
        );
    }
}
